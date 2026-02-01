// Barcode Scanner using html5-qrcode library
let html5QrcodeScanner = null;
let isScanning = false;

function startBarcodeScanner() {
    if (isScanning) {
        return;
    }

    // Show scanner container
    document.getElementById('barcode-scanner').style.display = 'block';
    document.getElementById('start-scan-btn').style.display = 'none';
    document.getElementById('scanner-status').textContent = '📷 Initializing camera...';
    document.getElementById('scanner-result').style.display = 'none';

    // Configure scanner
    const config = {
        fps: 10,
        qrbox: { width: 250, height: 150 },
        aspectRatio: 1.777778,
        formatsToSupport: [
            Html5QrcodeSupportedFormats.EAN_13,
            Html5QrcodeSupportedFormats.EAN_8,
            Html5QrcodeSupportedFormats.UPC_A,
            Html5QrcodeSupportedFormats.UPC_E,
            Html5QrcodeSupportedFormats.CODE_128,
            Html5QrcodeSupportedFormats.CODE_39,
            Html5QrcodeSupportedFormats.QR_CODE
        ]
    };

    // Initialize scanner
    html5QrcodeScanner = new Html5Qrcode("scanner-region");

    // Start scanning
    html5QrcodeScanner.start(
        { facingMode: "environment" }, // Use back camera on mobile
        config,
        onScanSuccess,
        onScanFailure
    ).then(() => {
        isScanning = true;
        document.getElementById('scanner-status').textContent = '📷 Camera ready! Position barcode in the frame';
        document.getElementById('scanner-status').style.backgroundColor = '#e6ffe6';
        document.getElementById('scanner-status').style.color = 'green';
    }).catch(err => {
        console.error("Camera error:", err);
        document.getElementById('scanner-status').textContent = '❌ Camera access denied or not available';
        document.getElementById('scanner-status').style.backgroundColor = '#ffe6e6';
        document.getElementById('scanner-status').style.color = 'red';
        alert("Camera access is required to scan barcodes. Please allow camera permission or enter the barcode manually.");
        stopBarcodeScanner();
    });
}

function onScanSuccess(decodedText, decodedResult) {
    // Barcode detected successfully
    console.log(`Barcode detected: ${decodedText}`, decodedResult);
    
    // Fill the barcode field
    document.getElementById('barcode_scanned').value = decodedText;
    
    // Update verification method
    document.getElementById('verification_method').value = 'Barcode';
    
    // Show success message
    document.getElementById('scanner-result').textContent = `✅ Barcode Detected: ${decodedText}`;
    document.getElementById('scanner-result').style.display = 'block';
    document.getElementById('scanner-status').textContent = '✅ Barcode scanned successfully!';
    document.getElementById('scanner-status').style.backgroundColor = '#e6ffe6';
    document.getElementById('scanner-status').style.color = 'green';
    
    // Play success sound (optional)
    playBeep();
    
    // Auto-stop scanner after successful scan
    setTimeout(() => {
        stopBarcodeScanner();
    }, 1500);
}

function onScanFailure(error) {
    // Scanning in progress (not an error, just no barcode detected yet)
    // Don't log every failure to avoid console spam
}

function stopBarcodeScanner() {
    if (html5QrcodeScanner && isScanning) {
        html5QrcodeScanner.stop().then(() => {
            html5QrcodeScanner.clear();
            isScanning = false;
            document.getElementById('barcode-scanner').style.display = 'none';
            document.getElementById('start-scan-btn').style.display = 'inline-block';
        }).catch(err => {
            console.error("Error stopping scanner:", err);
            isScanning = false;
            document.getElementById('barcode-scanner').style.display = 'none';
            document.getElementById('start-scan-btn').style.display = 'inline-block';
        });
    } else {
        document.getElementById('barcode-scanner').style.display = 'none';
        document.getElementById('start-scan-btn').style.display = 'inline-block';
    }
}

function playBeep() {
    // Create a simple beep sound
    try {
        const audioContext = new (window.AudioContext || window.webkitAudioContext)();
        const oscillator = audioContext.createOscillator();
        const gainNode = audioContext.createGain();
        
        oscillator.connect(gainNode);
        gainNode.connect(audioContext.destination);
        
        oscillator.frequency.value = 800;
        oscillator.type = 'sine';
        
        gainNode.gain.setValueAtTime(0.3, audioContext.currentTime);
        gainNode.gain.exponentialRampToValueAtTime(0.01, audioContext.currentTime + 0.1);
        
        oscillator.start(audioContext.currentTime);
        oscillator.stop(audioContext.currentTime + 0.1);
    } catch (e) {
        // Beep failed, no problem
    }
}

// Clean up on page unload
window.addEventListener('beforeunload', () => {
    if (html5QrcodeScanner && isScanning) {
        html5QrcodeScanner.stop();
    }
});
