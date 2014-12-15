function CheckElement(ElemID,condicao) {
   var Elemento = document.getElementById(ElemID);
	if (condicao) {
		Elemento.checked = true;
	}else {
		Elemento.checked = false;
	}
}

 function PrintDiv(divID) {
     var divElements = document.getElementById(divID).innerHTML;
     var oldPage = document.body.innerHTML;
     document.body.innerHTML = 
       "<html><head><title></title></head><body>" + 
       divElements + "</body>";
     window.print();
     document.body.innerHTML = oldPage;
 }

function EmUpperCase(obj){
	obj.value = obj.value.toUpperCase();
}	
