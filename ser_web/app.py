from flask import Flask, render_template, request, redirect, session, url_for, flash
from flask_mysqldb import MySQL
import os
from model import detect
import MySQLdb
from werkzeug.utils import secure_filename

app = Flask(__name__)
app.secret_key = 'your_secret_key'

# MySQL Configurations
app.config['MYSQL_HOST'] = 'localhost'
app.config['MYSQL_USER'] = 'root'
app.config['MYSQL_PASSWORD'] = ''
app.config['MYSQL_DB'] = 'employee_db'

mysql = MySQL(app)

# Uploads folder
UPLOAD_FOLDER = os.path.join('static', 'uploads')
app.config['UPLOAD_FOLDER'] = UPLOAD_FOLDER

# Allowed file extensions
ALLOWED_EXTENSIONS = {'wav', 'mp3'}

def allowed_file(filename):
    """Check if the uploaded file is allowed."""
    return '.' in filename and filename.rsplit('.', 1)[1].lower() in ALLOWED_EXTENSIONS

@app.route('/')
def home():
    return render_template('home.html')

@app.route('/login', methods=['GET', 'POST'])
def login():
    if request.method == 'POST':
        employee_id = request.form['employee_id']
        cur = mysql.connection.cursor(MySQLdb.cursors.DictCursor)
        cur.execute("SELECT * FROM employees WHERE id = %s", [employee_id])
        employee = cur.fetchone()

        if employee:
            session['employee_id'] = employee['id']
            return redirect(url_for('dashboard'))
        else:
            flash('Invalid Employee ID')
            return redirect(url_for('login'))

    return render_template('login.html')

@app.route('/dashboard')
def dashboard():
    if 'employee_id' not in session:
        return redirect(url_for('login'))

    employee_id = session['employee_id']
    cur = mysql.connection.cursor(MySQLdb.cursors.DictCursor)
    cur.execute("SELECT * FROM employees WHERE id = %s", [employee_id])
    employee = cur.fetchone()

    # Retrieve emotion from session if it exists
    emotion = session.pop('emotion', None)  # Clear emotion after use

    return render_template('dashboard.html', employee=employee, emotion=emotion)

@app.route('/upload', methods=['POST'])
def upload():
    if 'employee_id' not in session:
        return redirect(url_for('login'))

    if 'audio' not in request.files:
        flash('No file part')
        return redirect(url_for('dashboard'))

    file = request.files['audio']
    if file.filename == '':
        flash('No selected file')
        return redirect(url_for('dashboard'))

    if file and allowed_file(file.filename):
        filename = secure_filename(file.filename)
        filepath = os.path.join(app.config['UPLOAD_FOLDER'], filename)

        # Handle file name collisions
        counter = 1
        while os.path.exists(filepath):
            name, ext = os.path.splitext(filename)
            filename = f"{name}_{counter}{ext}"
            filepath = os.path.join(app.config['UPLOAD_FOLDER'], filename)
            counter += 1

        file.save(filepath)
        
        # Emotion detection
        emotion = detect.classify_emotion(filepath)
        print(f"Detected Emotion: {emotion}")  # Debugging line
        
        session['emotion'] = emotion
        update_satisfaction(session['employee_id'], emotion)

        flash(f'Emotion detected: {emotion}')
        return redirect(url_for('dashboard'))
    else:
        flash('Invalid file format. Please upload a .wav or .mp3 file.')
        return redirect(url_for('dashboard'))


@app.route('/analyze')
def analyze():
    cur = mysql.connection.cursor(MySQLdb.cursors.DictCursor)
    cur.execute("SELECT * FROM employees")  # Query to get all employees
    employees = cur.fetchall()
    return render_template('analyze.html', employees=employees)

@app.route('/performance/<int:employee_id>')
def performance(employee_id):
    cur = mysql.connection.cursor(MySQLdb.cursors.DictCursor)
    cur.execute("SELECT * FROM employees WHERE id = %s", [employee_id])
    employee = cur.fetchone()

    return render_template('performance.html', employee=employee)

def update_satisfaction(employee_id, emotion):
    cur = mysql.connection.cursor()
    if emotion in ['happy', 'neutral']:
        cur.execute("UPDATE employees SET satisfaction_count = satisfaction_count + 1 WHERE id = %s", [employee_id])
    else:
        cur.execute("UPDATE employees SET dissatisfaction_count = dissatisfaction_count + 1 WHERE id = %s", [employee_id])
    mysql.connection.commit()
    
@app.route('/leaderboard')
def leaderboard():
    cur = mysql.connection.cursor(MySQLdb.cursors.DictCursor)
    
    # Query to get top 3 employees based on satisfaction count in descending order
    cur.execute("SELECT * FROM employees ORDER BY satisfaction_count DESC LIMIT 3")
    top_employees = cur.fetchall()

    # Query to get worst 3 employees based on dissatisfaction count in ascending order
    cur.execute("SELECT * FROM employees ORDER BY dissatisfaction_count DESC LIMIT 3")
    worst_employees = cur.fetchall()

    return render_template('leaderboard.html', top_employees=top_employees, worst_employees=worst_employees)


if __name__ == '__main__':
    app.run(debug=True)
