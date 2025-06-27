

function setupDropCaps() {
		
	//var dropCaps = Array.from(document.getElementsByClassName('illuminated'));
	let dropCapContainer  = Array.from(document.getElementsByClassName('drop-cap-container'));
	
	if (dropCapContainer.length) {
		//Find the first letter of the first paragraph within the container.
		dropCapContainer.forEach(function (container) {
			let initialParagraph = container.querySelector('p'),
			    initial = initialParagraph.innerHTML.charAt(0),
				span = document.createElement('span'),
				content = document.createTextNode(initial);

			initialParagraph.classList.add('drop-cap');
			span.classList.add('dc');
			span.classList.add('dc-'+initial.toLowerCase());
			span.appendChild(content);
			initialParagraph.textContent = initialParagraph.textContent.substring(1);
			initialParagraph.insertBefore(span, initialParagraph.firstChild);

		});
	}
}

document.addEventListener("DOMContentLoaded", function () {
	setupDropCaps();
});