<div id="pdf-container"></div>

<script>
	var pdf = new PDFAnnotate("pdf-container", "<?=base_url('document/'.$dokumen)?>", {
    // options here// enable moving tool
		pdf.enableSelector();

// Enable pencil tool
		pdf.enablePencil(); 

// Enable comment/note tool
		pdf.enableAddText(); 

// Enable arrow tool
		pdf.enableAddArrow(); 

// Enable rectangle tool 
		pdf.enableRectangle();

// Add an image to the PDF
		pdf.addImageToCanvas();

// Delete the selected annotation
		pdf.deleteSelectedObject(); 

// Remove all annotations
pdf.clearActivePage(); // Clear current page

// Save the PDF with annotations
pdf.savePdf(); 

// Serialize PDF annotation data
pdf.serializePdf();

// Load annotations from JSON
pdf.loadFromJSON(serializedJSON);

// Set color for tools
pdf.setColor(color);

// Set border color for rectangle tool
pdf.setBorderColor(color);

// Set brush size for pencil tool
pdf.setBrushSize(width);

// Set font size for text tool
pdf.setFontSize(font_size);

// Set border size of rectangles
pdf.setBorderSize(border_size);
});
</script>