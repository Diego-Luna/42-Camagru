console.log("photo.js loaded");

let isWebcamMode = true;
let selectedSticker = null;
let stickersOnCanvas = [];
let baseImageData = null;
let draggingIndex = null;
let offsetX = 0;
let offsetY = 0;

const STICKER_WIDTH = 100;
const STICKER_HEIGHT = 100;

const video = document.getElementById('video');
const canvas = document.getElementById('canvas');
const preview = document.getElementById('preview');
const fileInput = document.getElementById('fileInput');
const saveBtn = document.getElementById('saveBtn');
const toggleBtn = document.getElementById('toggleMode');
const captureBtn = document.getElementById('captureBtn');

async function startWebcam() {
  try {
    const stream = await navigator.mediaDevices.getUserMedia({ video: true });
    video.srcObject = stream;
    await video.play();
    // Verifica botones
    updateButtons();
  } catch (err) {
    console.error("Webcam error:", err);
    switchToUpload();
  }
}

function switchToUpload() {
  isWebcamMode = false;
  video.classList.add('hidden');
  fileInput.classList.remove('hidden');
  toggleBtn.textContent = 'Use Webcam';

  if (video.srcObject) {
    video.srcObject.getTracks().forEach(track => track.stop());
  }
  updateButtons();
}

function switchToWebcam() {
  isWebcamMode = true;
  video.classList.remove('hidden');
  fileInput.classList.add('hidden');
  toggleBtn.textContent = 'Use Upload';
  startWebcam();
  updateButtons();
}

toggleBtn.onclick = () => {
  if (isWebcamMode) switchToUpload(); 
  else switchToWebcam();
};

fileInput.onchange = e => {
    if (!selectedSticker || !e.target.files[0]) return;
    
    const reader = new FileReader();
    reader.onload = evt => {
        const img = new Image();
        img.onload = () => {
            initCanvas(img);
            const centerX = (canvas.width / 2) - (STICKER_WIDTH / 2);
            const centerY = (canvas.height / 2) - (STICKER_HEIGHT / 2);
            
            stickersOnCanvas.push({
                path: selectedSticker,
                x: centerX,
                y: centerY
            });
            redrawCanvas();
        };
        img.src = evt.target.result;
    };
    reader.readAsDataURL(e.target.files[0]);
};

captureBtn.onclick = () => {
    if (!selectedSticker) return;
    initCanvas(video);

    const centerX = (canvas.width / 2) - (STICKER_WIDTH / 2);
    const centerY = (canvas.height / 2) - (STICKER_HEIGHT / 2);
    
    stickersOnCanvas.push({
        path: selectedSticker,
        x: centerX,
        y: centerY
    });
    redrawCanvas();
};

function initCanvas(source) {
  canvas.width = source.videoWidth || source.width;
  canvas.height = source.videoHeight || source.height;
  const ctx = canvas.getContext('2d');
  ctx.drawImage(source, 0, 0);
  baseImageData = ctx.getImageData(0, 0, canvas.width, canvas.height);
  canvas.classList.remove('hidden');
  redrawCanvas();
  updateButtons();
}

document.querySelectorAll('.sticker').forEach(stickerElem => {
    stickerElem.onclick = () => {
        document.querySelectorAll('.sticker').forEach(s => 
            s.classList.remove('border-blue-500'));
        
        stickerElem.classList.add('border-blue-500');
        selectedSticker = stickerElem.dataset.src;
        
        if (baseImageData) {
            const centerX = (canvas.width / 2) - (STICKER_WIDTH / 2);
            const centerY = (canvas.height / 2) - (STICKER_HEIGHT / 2);
            
            stickersOnCanvas.push({
                path: selectedSticker,
                x: centerX,
                y: centerY
            });
            redrawCanvas();
        }
        
        updateButtons();
    };
});


canvas.addEventListener('mousedown', e => {
  if (!baseImageData) return;
  const rect = canvas.getBoundingClientRect();
  const scaleX = canvas.width / rect.width;
  const scaleY = canvas.height / rect.height;
  const clickX = (e.clientX - rect.left) * scaleX;
  const clickY = (e.clientY - rect.top) * scaleY;

  for (let i = stickersOnCanvas.length - 1; i >= 0; i--) {
    const sticker = stickersOnCanvas[i];
    if (
      clickX >= sticker.x &&
      clickX <= sticker.x + STICKER_WIDTH &&
      clickY >= sticker.y &&
      clickY <= sticker.y + STICKER_HEIGHT
    ) {
      draggingIndex = i;
      offsetX = clickX - sticker.x;
      offsetY = clickY - sticker.y;
      canvas.style.cursor = 'grabbing';
      break;
    }
  }
});


canvas.addEventListener('mousemove', e => {
  if (draggingIndex === null) return;

  const rect = canvas.getBoundingClientRect();
  const scaleX = canvas.width / rect.width;
  const scaleY = canvas.height / rect.height;
  const moveX = (e.clientX - rect.left) * scaleX;
  const moveY = (e.clientY - rect.top) * scaleY;

  stickersOnCanvas[draggingIndex].x = moveX - offsetX;
  stickersOnCanvas[draggingIndex].y = moveY - offsetY;

  redrawCanvas();
});


canvas.addEventListener('mouseup', () => {
  draggingIndex = null;
  canvas.style.cursor = 'default';
});

function redrawCanvas() {
  if (!baseImageData) return;
  const ctx = canvas.getContext('2d');
  ctx.putImageData(baseImageData, 0, 0);

  Promise.all(stickersOnCanvas.map(sticker => {
    return new Promise(resolve => {
      const img = new Image();
      img.onload = () => {
        ctx.drawImage(img, sticker.x, sticker.y, STICKER_WIDTH, STICKER_HEIGHT);
        resolve();
      };
      img.src = sticker.path;
    });
  })).then(() => {
    preview.innerHTML = '';
    preview.appendChild(canvas);
    updateButtons();
  });
}

saveBtn.onclick = () => {
    if (!baseImageData || stickersOnCanvas.length === 0) return;
    
    canvas.toBlob(async (blob) => {
        const formData = new FormData();
        formData.append('image', blob, 'image.png');

        stickersOnCanvas.forEach((sticker, index) => {
            formData.append(`stickers[${index}][path]`, sticker.path);
            formData.append(`stickers[${index}][x]`, sticker.x);
            formData.append(`stickers[${index}][y]`, sticker.y);
        });
        
        try {
            const response = await fetch('controllers/process_image.php', {
                method: 'POST',
                body: formData
            });
            
            let data;
            const contentType = response.headers.get('content-type');
            
            if (contentType && contentType.includes('application/json')) {
                data = await response.json();
            } else {
                const text = await response.text();
                console.error('Invalid response:', text);
                throw new Error('Server did not return JSON');
            }
            
            if (data.success) {
                window.location.href = 'index.php';
            } else {
                alert('Error saving image: ' + (data.error || 'Unknown error'));
            }
        } catch (error) {
            console.error('Error:', error);
            alert('Failed to save image: ' + error.message);
        }
    }, 'image/png');
};

function updateButtons() {
  
  captureBtn.disabled = !selectedSticker;
  toggleBtn.disabled = !selectedSticker;
  
  saveBtn.disabled = (!baseImageData || stickersOnCanvas.length === 0);
}

updateButtons();
startWebcam();
