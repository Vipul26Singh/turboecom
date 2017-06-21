function addRow(id, btn){
	var elements  = document.getElementById(id).cells[4].innerHTML;       
	
	var row = btn.parentNode.parentNode;
	row.parentNode.removeChild(row);

	$.ajax({
                    'type': 'POST',
		    'async': false,
                    'url': '../modules/turboecom/addProduct.php',
                    'data': {'post_data':elements},
                    error: function(xhr, status, error) {
                        alert(xhr.responseText);
                    },
                    success: function(data) {
			alert(data);
                    }
    });
}

