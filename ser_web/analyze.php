<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Performance Analysis</title>
    <link rel="stylesheet" href="{{ url_for('static', filename='styles.css') }}">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script> <!-- Include Chart.js -->
    <style>
        body {
            font-family: Arial, sans-serif; /* Set a clean font */
            background-color: #f9f9f9; /* Light background for contrast */
        }
        nav {
            background-color: #007bff; /* Bright blue navigation bar */
            padding: 10px; /* Padding around nav items */
            color: white; /* White text color */
        }
        nav ul {
            list-style-type: none; /* Remove bullets from list */
            padding: 0; /* Remove padding */
            margin: 0; /* Remove margin */
            display: flex; /* Display items in a row */
            justify-content: center; /* Center nav items */
        }
        nav li {
            margin: 0 15px; /* Space between nav items */
        }
        nav a {
            color: white; /* Text color */
            text-decoration: none; /* Remove underline */
            font-weight: bold; /* Bold text */
        }
        nav a:hover {
            text-decoration: underline; /* Underline on hover */
        }
        .container {
            max-width: 800px;
            margin: 20px auto;
            padding: 20px;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        th, td {
            padding: 12px 15px; /* Increased padding for a better look */
            border: 1px solid #ddd;
            text-align: left;
        }
        th {
            background-color: #007bff; /* Bright blue for header */
            color: white; /* White text for header */
        }
        .highlight {
            background-color: #d4edda; /* Light green background for highlighting */
        }
        .chart-container {
            position: relative; /* Position for relative chart placement */
            margin-top: 20px; /* Space above each chart */
        }
    </style>
</head>
<body>
    <!-- Navigation Bar -->
    <nav>
        <ul>
            <li><a href="{{ url_for('home') }}">Home</a></li>
            <li><a href="{{ url_for('login') }}">Login</a></li>
            <li><a href="{{ url_for('dashboard') }}">Dashboard</a></li>
            <li><a href="{{ url_for('analyze') }}">Performance Analysis</a></li>
        </ul>
    </nav>

    <div class="container">
        <h1>Employee Performance Analysis</h1>
        <table>
            <thead>
                <tr>
                    <th>Employee ID</th>
                    <th>Name</th>
                    <th>Satisfaction Count</th>
                    <th>Dissatisfaction Count</th>
                    <th>Performance Chart</th> <!-- New column for performance chart -->
                </tr>
            </thead>
            <tbody>
                {% for employee in employees %}
                <tr class="{% if employee.satisfaction_count > 10 %}highlight{% endif %}">
                    <td>{{ employee.id }}</td>
                    <td>{{ employee.name }}</td>
                    <td>{{ employee.satisfaction_count }}</td>
                    <td>{{ employee.dissatisfaction_count }}</td>
                    <td>
                        <div class="chart-container">
                            <canvas id="chart-{{ employee.id }}" width="100" height="100"></canvas> <!-- Canvas for each employee's chart -->
                        </div>
                        <script>
                            const ctx = document.getElementById('chart-{{ employee.id }}').getContext('2d');
                            const chart = new Chart(ctx, {
                                type: 'pie',
                                data: {
                                    labels: ['Satisfied', 'Dissatisfied'],
                                    datasets: [{
                                        label: 'Satisfaction Levels',
                                        data: [
                                            {{ employee.satisfaction_count | default(0) }},
                                            {{ employee.dissatisfaction_count | default(0) }}
                                        ],
                                        backgroundColor: [
                                            'rgba(76, 175, 80, 0.6)', // Satisfied - green
                                            'rgba(255, 82, 82, 0.6)'  // Dissatisfied - red
                                        ],
                                        borderColor: [
                                            'rgba(76, 175, 80, 1)',
                                            'rgba(255, 82, 82, 1)'
                                        ],
                                        borderWidth: 2
                                    }]
                                },
                                options: {
                                    responsive: true,
                                    plugins: {
                                        legend: {
                                            display: false // Hide legend if needed
                                        }
                                    }
                                }
                            });
                        </script>
                    </td>
                </tr>
                {% endfor %}
            </tbody>
        </table>
    </div>
</body>
</html>
