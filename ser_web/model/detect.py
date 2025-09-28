import numpy as np
import librosa
import tensorflow as tf

# Load the trained model
model = tf.keras.models.load_model('C:\wamp64\www\ser_web\model\speech_emotion_recognition_model1.1.h5')

# List of emotions to map to (must match the training set)
emotions = ['angry', 'fear', 'happy', 'neutral', 'sad']

# Function to extract features from an audio file
def extract_features(file_path):
    try:
        audio_data, sample_rate = librosa.load(file_path, res_type='kaiser_fast')
        
        # Extract features
        mfccs = np.mean(librosa.feature.mfcc(y=audio_data, sr=sample_rate, n_mfcc=40).T, axis=0)
        chroma = np.mean(librosa.feature.chroma_stft(y=audio_data, sr=sample_rate).T, axis=0)
        spectral_contrast = np.mean(librosa.feature.spectral_contrast(y=audio_data, sr=sample_rate).T, axis=0)
        zero_crossing_rate = np.mean(librosa.feature.zero_crossing_rate(y=audio_data).T, axis=0)
        
        # Combine features into a single vector
        features = np.hstack((mfccs, chroma, spectral_contrast, zero_crossing_rate))
        
        return features
    except Exception as e:
        print(f"Error processing {file_path}: {e}")
        return None

# Function to classify emotion from an audio file
def classify_emotion(file_path):
    features = extract_features(file_path)
    
    if features is not None:
        # Reshape features for the model (1, number_of_features)
        features = features.reshape(1, -1)
        
        # Predict the emotion
        predictions = model.predict(features)
        predicted_class = np.argmax(predictions, axis=1)[0]
        
        # Map the predicted class to the corresponding emotion
        predicted_emotion = emotions[predicted_class]
        
        return predicted_emotion
    else:
        return "Error extracting features."
