

var viewcat = [];
var firstshow = 1;
function jsShowFunc(obj){
	var idNum = obj.id.match(/\d+/);
	var contentblock = document.getElementById('mi-cat'+idNum);
	if (obj.parentNode.classList.contains('mi-currentcat') === true && firstshow !== 0) {
		viewcat[idNum] = 1;
		firstshow = 0;
	}
	if (viewcat[idNum] !== 1){
		contentblock.style.display = 'block';
		contentblock.parentNode.classList.add('mi-viewing');
		contentblock.parentNode.style.backgroundColor = custColor;
		contentblock.parentNode.style.marginBottom = '10px';
		obj.style.backgroundPosition = '50% 0';
		viewcat[idNum] = 1;
	}else{
		contentblock.style.display = 'none';
		contentblock.parentNode.classList.remove('mi-viewing');
		contentblock.parentNode.classList.remove('mi-currentcat');
		contentblock.parentNode.style.marginBottom = '0';
		contentblock.parentNode.style.backgroundColor = 'transparent';
		obj.style.backgroundPosition = '50% 100%';
		viewcat[idNum] = 0;
	}

}

var custColor;
window.onload = function getColor(){
	var celem = document.getElementById("mi-color1"); 
	custColor = celem.style.backgroundColor;
}
