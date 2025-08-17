<?php
session_start();
require_once 'config.php';

// Check if user is logged in
if (!is_logged_in()) {
    header('Location: login.php');
    exit();
}

$user = get_logged_in_user();
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Test Profile Picture Upload</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 p-8">
    <div class="max-w-2xl mx-auto">
        <h1 class="text-3xl font-bold text-center mb-8">Test Profile Picture Upload</h1>

        <div class="bg-white rounded-lg shadow-md p-6">
            <h2 class="text-xl font-semibold mb-4">Current User: <?php echo htmlspecialchars($user['full_name']); ?></h2>

            <div class="mb-6">
                <h3 class="font-semibold mb-2">Current Profile Picture:</h3>
                <img src="<?php echo htmlspecialchars($user['profile_image'] ?? 'data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMTIwIiBoZWlnaHQ9IjEyMCIgdmlld0JveD0iMCAwIDEyMCAxMjAiIGZpbGw9Im5vbmUiIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyI+CjxyZWN0IHdpZHRoPSIxMjAiIGhlaWdodD0iMTIwIiByeD0iNjAiIGZpbGw9IiNFNUU3RUIiLz4KPHN2ZyB4PSIzMCIgeT0iMjAiIHdpZHRoPSI2MCIgaGVpZ2h0PSI2MCIgdmlld0JveD0iMCAwIDI0IDI0IiBmaWxsPSIjOEE5M0E2Ij4KPHBhdGggZD0iTTEyIDJDNi40OCAyIDIgNi40OCAyIDEyczQuNDggMTAgMTAgMTAgMTAtNC40OCAxMC0xMFMxNy41MiAyIDEyIDJ6bTAgM2MyLjY3IDAgNC44MyAyLjE2IDQuODMgNC44M1MxNC42NyAxNC42NiAxMiAxNC42NiA3LjE3IDEyLjUgNy4xNyA5LjgzIDkuMzMgNS4xNyAxMiA1LjE3em0wIDEyYzQuNDIgMCA4LjE3LTIuMTYgOC4xNy00Ljgzcy0zLjc1LTQuODMtOC4xNy00LjgzLTguMTcgMi4xNi04LjE3IDQuODNTNy41OCAyMC4xNyAxMiAyMC4xN3oiLz4KPC9zdmc+Cjwvc3ZnPgo='); ?>"
                     alt="Profile Picture"
                     class="w-32 h-32 rounded-full object-cover border-4 border-gray-200"
                     onerror="this.src='data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMTIwIiBoZWlnaHQ9IjEyMCIgdmlld0JveD0iMCAwIDEyMCAxMjAiIGZpbGw9Im5vbmUiIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyI+CjxyZWN0IHdpZHRoPSIxMjAiIGhlaWdodD0iMTIwIiByeD0iNjAiIGZpbGw9IiNFNUU3RUIiLz4KPHN2ZyB4PSIzMCIgeT0iMjAiIHdpZHRoPSI2MCIgaGVpZ2h0PSI2MCIgdmlld0JveD0iMCAwIDI0IDI0IiBmaWxsPSIjOEE5M0E2Ij4KPHBhdGggZD0iTTEyIDJDNi40OCAyIDIgNi40OCAyIDEyczQuNDggMTAgMTAgMTAgMTAtNC40OCAxMC0xMFMxNy41MiAyIDEyIDJ6bTAgM2MyLjY3IDAgNC44MyAyLjE2IDQuODMgNC44M1MxNC42NyAxNC42NiAxMiAxNC42NiA3LjE3IDEyLjUgNy4xNyA5LjgzIDkuMzMgNS4xNyAxMiA1LjE3em0wIDEyYzQuNDIgMCA4LjE3LTIuMTYgOC4xNy00Ljgzcy0zLjc1LTQuODMtOC4xNy00LjgzLTguMTcgMi4xNi04LjE3IDQuODNTNy41OCAyMC4xNyAxMiAyMC4xN3oiLz4KPC9zdmc+Cjwvc3ZnPgo=';">
            </div>

            <div class="mb-6">
                <h3 class="font-semibold mb-2">Upload New Picture:</h3>
                <input type="file" id="profile-picture-input" accept="image/*" class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                <button id="upload-btn" class="mt-2 bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                    <i class="fas fa-upload mr-2"></i>Upload Picture
                </button>
            </div>

            <div id="upload-status" class="hidden">
                <div class="flex items-center">
                    <div class="animate-spin rounded-full h-4 w-4 border-b-2 border-blue-500 mr-2"></div>
                    <span>Uploading...</span>
                </div>
            </div>

            <div id="upload-result" class="mt-4"></div>

            <div class="mt-6 pt-6 border-t border-gray-200">
                <h3 class="font-semibold mb-2">Debug Information:</h3>
                <div class="text-sm text-gray-600 space-y-1">
                    <p>User ID: <?php echo $user['id']; ?></p>
                    <p>Current Profile Image: <?php echo htmlspecialchars($user['profile_image'] ?? 'None'); ?></p>
                    <p>Upload Directory: uploads/profile_pictures/</p>
                    <p>Max File Size: 5MB</p>
                    <p>Allowed Types: JPG, PNG, GIF</p>
                </div>
            </div>

            <div class="mt-4">
                <a href="patient/profile.php" class="text-blue-500 hover:text-blue-700">
                    <i class="fas fa-arrow-left mr-1"></i>Back to Profile
                </a>
            </div>
        </div>
    </div>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const fileInput = document.getElementById('profile-picture-input');
        const uploadBtn = document.getElementById('upload-btn');
        const uploadStatus = document.getElementById('upload-status');
        const uploadResult = document.getElementById('upload-result');
        const profileImage = document.querySelector('img');

        uploadBtn.addEventListener('click', function() {
            const file = fileInput.files[0];
            if (!file) {
                showResult('Please select a file first.', 'error');
                return;
            }

            // Show loading
            uploadStatus.classList.remove('hidden');
            uploadResult.innerHTML = '';

            const formData = new FormData();
            formData.append('profile_picture', file);

            fetch('upload_profile_picture.php', {
                method: 'POST',
                body: formData
            })
            .then(response => {
                console.log('Response status:', response.status);
                return response.json();
            })
            .then(data => {
                console.log('Response data:', data);
                uploadStatus.classList.add('hidden');

                if (data.success) {
                    showResult('Upload successful!', 'success');
                    // Update the image
                    profileImage.src = data.image_url + '?t=' + new Date().getTime();
                } else {
                    showResult('Upload failed: ' + (data.message || 'Unknown error'), 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                uploadStatus.classList.add('hidden');
                showResult('Network error: ' + error.message, 'error');
            });
        });

        function showResult(message, type) {
            const className = type === 'success' ? 'text-green-600' : 'text-red-600';
            uploadResult.innerHTML = `<p class="${className}">${message}</p>`;
        }
    });
    </script>
</body>
</html>
