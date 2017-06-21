function addRow(id, btn){
	var id_data = 'data_'+id;
	var elements  = document.getElementById(id_data).innerHTML;       
	var id_add_button = id+'_add_button';
	var row = btn.parentNode.parentNode;


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

	row.parentNode.removeChild(row);

}

