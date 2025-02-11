let isWebcamMode = true;
let selectedSticker = null;
let stickersOnCanvas = [];
let baseImageData = null;
let draggingIndex = null;
let offsetX = 0;
let offsetY = 0;
let stateButtons = 0;

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
    updateButtons();
  } catch (err) {
    // console.error("Webcam error:", err);
    alert("Webcam error:" + err);
    switchToUpload();
  }
}

function switchToUpload() {
  isWebcamMode = false;
  video.style.display = 'none';
  canvas.classList.add('d-none');
  fileInput.classList.remove('d-none');
  toggleBtn.textContent = 'Use Webcam';
  captureBtn.classList.add('d-none');
  preview.innerHTML = '';

  if (video.srcObject) {
    video.srcObject.getTracks().forEach(track => track.stop());
  }
  updateButtons();
}

function switchToWebcam() {
  isWebcamMode = true;
  video.style.display = 'block';
  canvas.classList.add('d-none');
  fileInput.classList.add('d-none');
  toggleBtn.textContent = 'Use Upload';
  captureBtn.classList.remove('d-none');
  preview.innerHTML = '';
  startWebcam();
  updateButtons();
}

toggleBtn.onclick = () => {
  if (isWebcamMode) switchToUpload(); 
  else switchToWebcam();
};

fileInput.onchange = e => {
  if (!selectedSticker || !e.target.files[0]) return;

  const MAX_FILE_SIZE = 1 * 1024 * 1024;
  const file = e.target.files[0];

  if (file.size > MAX_FILE_SIZE) {
    alert("The image exceeds the maximum allowed size of 1 MB.");
    fileInput.value = ''; 
    return
  }
  
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
  const ctx = canvas.getContext('2d', { willReadFrequently: true });
  ctx.drawImage(source, 0, 0);
  baseImageData = ctx.getImageData(0, 0, canvas.width, canvas.height);
  
  canvas.classList.remove('d-none');

  // Ocultar video y mostrar canvas
  video.style.display = 'none';
  canvas.classList.remove('d-none');
  preview.innerHTML = '';
  preview.appendChild(canvas);


  redrawCanvas();
  updateButtons();
}

document.querySelectorAll('.sticker').forEach(stickerElem => {
  stickerElem.onclick = () => {
    document.querySelectorAll('.sticker').forEach(s => 
      s.classList.remove('border', 'border-primary'));
    
    stickerElem.classList.add('border', 'border-primary');
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

canvas.addEventListener('dblclick', e => {
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
      if (stickersOnCanvas.length > 1) {
        stickersOnCanvas.splice(i, 1);
        redrawCanvas();
      } else {
        alert("There must be at least one sticker on the canvas.");
      }
      break;
    }
  }
});

function redrawCanvas() {
  if (!baseImageData) return;
  const ctx = canvas.getContext('2d');
  // Restore the base image
  ctx.putImageData(baseImageData, 0, 0);

  // Draw each sticker in its position
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
  if (!baseImageData || stickersOnCanvas.length === 0)
    return alert("Please place at least one sticker before saving.");

  canvas.toBlob(async (blob) => {
    const formData = new FormData();
    console.log("formData");
    console.log(formData);
    
    formData.append('image', blob, 'image.png');
    console.log(formData);

    stickersOnCanvas.forEach((sticker, index) => {
      formData.append(`stickers[${index}][path]`, sticker.path);
      formData.append(`stickers[${index}][x]`, sticker.x);
      formData.append(`stickers[${index}][y]`, sticker.y);
    });
    console.log(formData);

    try {
      const response = await fetch('controllers/process_image.php', {
        method: 'POST',
        body: formData
      });
      
      const contentType = response.headers.get('content-type');
      let data;
      if (contentType && contentType.includes('application/json')) {
        data = await response.json();
      } else {
        data = { error: 'Invalid response, expected JSON.' };
      }

      // Show message if creation was successful, otherwise error
      if (data.success) {
        alert("Image created successfully!");
        window.location.reload();  // Reload page on success
      } else {
        alert(data.error || "Error creating the image.");
      }

    } catch (error) {
      console.error(error);
      alert("diego An error occurred while creating the image." + error
      );
    }
  }, 'image/png');
};

function updateButtons() {
  captureBtn.disabled = !selectedSticker;
  toggleBtn.disabled = !selectedSticker;
  if (!selectedSticker) {
    stateButtons = 0;
  }
  saveBtn.disabled = (!baseImageData || stickersOnCanvas.length === 0);
}

// Confirm delete forms
document.addEventListener('DOMContentLoaded', () => {
  const deleteForms = document.querySelectorAll('.confirm-delete-form');
  deleteForms.forEach(form => {
    form.addEventListener('submit', event => {
      const message = form.getAttribute('data-confirm-message') || 'Are you sure?';
      if (!confirm(message)) {
        event.preventDefault();
      }
    });
  });
});

updateButtons();
startWebcam();