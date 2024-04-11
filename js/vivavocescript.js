

window.onload = function() {
    console.log("RUNNING");
    let mediarecorder;
    const parts = [];
    let stream = null; // Keep track of the current stream


    // Initially hide the stop and retake buttons
    document.getElementById("stoprecording").style.display = 'none';
    document.getElementById("retakevideo").style.display = 'none';

    // Attempt to get user media //NOTE ADD BACK AUDIO
    navigator.mediaDevices.getUserMedia({ video: true, audio: true })
    .then(s => {
        stream = s;
        const videoElement = document.getElementById("video");
        videoElement.srcObject = stream;

        // Initialize buttons
        document.getElementById("startrecording").onclick = function() {
            // Reset parts in case of a retake
            parts.length = 0;
            mediarecorder = new MediaRecorder(stream);
            mediarecorder.ondataavailable = function(e) {
                parts.push(e.data);
            };
            mediarecorder.start(2000);

            // Hide this button and show the stop button
            this.style.display = 'none';
            document.getElementById("stoprecording").style.display = 'block';
        };

        // Define retake video functionality
        document.getElementById("retakevideo").onclick = function() {


            // Show the start recording button
            const startButton = document.getElementById("startrecording");
            if (startButton) {
                startButton.style.display = 'block';
            } else {
                // Recreate the start recording button if it does not exist
                // This part depends on your existing setup
            }
            
            // Hide the retake video button itself
            this.style.display = 'none';

            // Reset the video element for a new recording
            const videoElement = document.getElementById("video");
            videoElement.srcObject = stream;
            videoElement.controls = false; // Hide controls during recording
            videoElement.autoplay = true; // Autoplay the live feed
            
            // Remove the existing download link
            const existingDownloadLink = document.getElementById("downloadLink");
            if (existingDownloadLink) {
                existingDownloadLink.remove();
            }
        };

    })
    
    .catch(err =>
            {
                console.error(err);
                alert("Error accessing the camera: " + err.message);
            }

        );

        document.getElementById("stoprecording").onclick = function() {
            mediarecorder.stop(); // Stop recording
    
            mediarecorder.onstop = () => {
                // This code now executes after the mediarecorder has stopped
                const blob = new Blob(parts, {type: "video/MP4 "});
                const url = URL.createObjectURL(blob);
                

                // Retrieve the user's name from the hidden input field
                var studentName = document.querySelector('input[name="student_name"]').value;
                const courseId = document.querySelector('input[name="courseid"]').value;

                const pageNumberInput = document.querySelector('input[name="question_number"]');
                const pageNumber = pageNumberInput ? pageNumberInput.value : 1; // Set a default value if input field doesn't exist
                var currentQuestion = document.getElementById('currentQuestion').value;
                const filename = studentName + "_course_" + courseId + "_question_" + pageNumber + ".mp4";


                const formData = new FormData();
                formData.append('video', blob, filename);
    
                fetch('/moodle/mod/vivavoce/awssubmit.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Failed to upload video');
                    }
                    return response.text();
                })
                .then(data => {
                    console.log('Video uploaded successfully:');
                    console.log("-------------------")
                    console.log(data)


                    const urlRegex = /(https?:\/\/[^\s]+)/;
                    const matches = data.match(urlRegex);
           
                    if (matches && matches.length > 0) {
                        var videoUrl = matches[0];

                        //makes local storage dict or retrives if already exists
                        var submittedVideos = JSON.parse(localStorage.getItem('submittedVideos')) || {};

                        submittedVideos[currentQuestion] = videoUrl;
                        localStorage.setItem('submittedVideos', JSON.stringify(submittedVideos));

                        console.log(submittedVideos);
                        



                    }
         
                    



                    

                })
                .catch(error => {
                    console.log("Error uploading video", error);
                });
    
                // Prepare for playback
                const videoElement = document.getElementById("video");
                videoElement.srcObject = null;
                videoElement.src = url;
                videoElement.controls = true;
                videoElement.autoplay = false; // Ensure user initiates playback
    
                // Handle download link creation/replacement
                let existingDownloadLink = document.getElementById("downloadLink");
                if (existingDownloadLink) {
                    existingDownloadLink.href = url; // Update link for new recording
                } else {
                    const downloadLink = document.createElement("a");
                    downloadLink.setAttribute("id", "downloadLink");
                    downloadLink.className = "download-link";
                    downloadLink.href = url;
                    downloadLink.download = "recordedVideo.webm";
                    downloadLink.appendChild(document.createTextNode("Download Recorded Video"));
                    document.getElementById("linkContainer").appendChild(downloadLink);
                }
    
                // UI adjustments post-recording
                document.getElementById("stoprecording").style.display = 'none'; // Hide stop button
                document.getElementById("retakevideo").style.display = 'block'; // Show retake button
            };
        };


    };