

$("#startLink").click(function () {
	var img = document.getElementById('selected-image');
	startRecognize(img);
});

function startRecognize(img){
	recognizeFile(img);
}

function recognizeFile(file){
  	const corePath = window.navigator.userAgent.indexOf("Edge") > -1
    ? 'assets/js/tesseract-core.asm.js'
    : 'assets/js/tesseract-core.wasm.js';


	const worker = new Tesseract.TesseractWorker({
		corePath,
	});

	worker.recognize(file,
		$("#langsel").val()
	)
	.progress(function(packet){
		console.info(packet)
		progressUpdate(packet)

	})
	.then(function(data){
		console.log(data)
		progressUpdate({ status: 'done', data: data })
	})
}