(function(){
	if(facelift == null){
  		var facelift = {
			init: function(){
				if(EE.facelift.draggable.enabled) facelift.draggable();
			},
			
			draggable: function(){
				$(".mainTable").each(function(index,table){
					
					table = $(table);
					
					var fields = {};
					var db = {
						'table' : EE.facelift.draggable.table,
						'field' : EE.facelift.draggable.field,
						'id'	: EE.facelift.draggable.id,
					}
					
					table.tableDnD({
						onDrop: function(t,r) {
							var i = 0;
							
							table.find("tbody tr").each(function(key,row){
								row = $(row);
								if(i % 2 == 0){
									if (row.hasClass('odd')) row.removeClass('odd');
									row.addClass('even');
								}else{
									if (row.hasClass('even')) row.removeClass('even');
									row.addClass('odd');
								}
							
								var href = row.find('td a:first').attr('href');
								var params = href.split('&');
								
								fields[i] = {};
							
								$.each(params,function(index,data){
									var fieldData = data.split('=');
									fields[i][fieldData[0]] = fieldData[1];
								});
								
								i++;
							});
							
							$.ajax({    //create an ajax request to load_page.php
						        type: 'POST',
						        url: location.href,
						        data: 'facelift_ajax=' + $.toJSON(fields) + '&facelift_db=' + $.toJSON(db),  //with the page number as a parameter
						        success: function(msg){
						            //console.log(msg);
						        }
						    });
						}
					});
				});
			}
		};
	    $(function($){ facelift.init(); });
	}
})();