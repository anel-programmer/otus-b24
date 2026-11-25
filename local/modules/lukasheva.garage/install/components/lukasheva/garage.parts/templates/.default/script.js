BX.namespace('Lukasheva.GarageParts'); // создали пространство имен для компонента

BX.Lukasheva.GarageParts = {
    send: function(dealId, gridId){

        
        // Получить инстанс грида по его ID
        let gridParts = BX.Main.gridManager.getInstanceById(gridId);
        if (!gridParts) {
            // грид не найден, выходим, чтобы не упасть на gridParts.getRows()
            BX.UI.Dialogs.MessageBox.alert(BX.message('GARAGE_PARTS_GRID_ERROR'));
            return false;
        }
        //Получить идентификаторы отмеченных строк
        let selectedRows = gridParts.getRows().getSelectedIds();   
        
        if (selectedRows.length <= 0){
            BX.UI.Dialogs.MessageBox.alert(BX.message('GARAGE_PARTS_SEND_ERROR'));
            return false;
        }
        
        BX.ajax.runComponentAction("lukasheva:garage.parts","createRequest", {
            mode: "class", 
            data: {product_ids : selectedRows,
                    deal_id : dealId,
            },  
            }).then(function (response) {
                //сюда будут приходить ответ, у которого status == 'success'
                let message = BX.message('GARAGE_PARTS_SEND_SUCCESS').replace('#NUM#', response.data);
                BX.UI.Dialogs.MessageBox.alert(message);
               
            }, function (response) {
                //сюда будут приходить все ответы, у которых status !== 'success'
                BX.UI.Dialogs.MessageBox.alert(response.errors[0].message);
            });
    },
   
}