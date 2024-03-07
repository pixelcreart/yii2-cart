const { jsPDF } = window.jspdf;

function demoFromHTML() {
    let pdf = new jsPDF();
    let source = document.getElementById('confirmation-print');
    let margins = [10, 10, 10, 10];
	let scale = 0.5;
	
	pdf.setFont('Newsreader', 'normal');

	pdf.html(source, {
		callback: function (doc) {
			doc.save('comprobante.pdf');
		},
		margin: margins,
		html2canvas: {
			scale: scale
		}
	});

    // function (dispose) {
        // dispose: object with X, Y of the last line add to the PDF 
        //          this allow the insertion of new lines after html
        // pdf.save('Test.pdf');
    // }, margins);
}