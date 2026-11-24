BX.namespace('Lukasheva.Garage'); // создали пространство имен для компонента

BX.Lukasheva.Garage = {
    fields: [
       // {code: 'CARMAKE', label: 'CARS_TABLE_LABEL_CARMAKE'},
        //{code: 'MODEL', label: 'CARS_TABLE_LABEL_MODEL'},
        {code: 'LICENSE_PLATE_NUMBER', label: 'CARS_TABLE_LABEL_PLATE'},
        {code: 'YEAR_OF_MANUFACTURE', label: 'CARS_TABLE_LABEL_YEAR'},
        {code: 'CAR_COLOR', label: 'CARS_TABLE_LABEL_COLOR'},
        {code: 'MILEAGE', label: 'CARS_TABLE_LABEL_MILEAGE'},
    ],
    createRow: function (labelCode, control, isSelect) {
        return BX.create('div', {
            props: {className: 'ui-ctl-container'},
            style: {marginBottom: '12px'},
            children: [
                BX.create('div', {props: {className: 'ui-ctl-label-text'}, text: BX.message(labelCode)}),
                BX.create('div', {
                    props: {className: isSelect
                        ? 'ui-ctl ui-ctl-after-icon ui-ctl-dropdown ui-ctl-w100'
                        : 'ui-ctl ui-ctl-textbox ui-ctl-w100'},
                    children: isSelect
                        ? [control, BX.create('div', {props: {className: 'ui-ctl-after ui-ctl-icon-angle'}})]
                        : [control],
                }),
            ],
        });
    },
    openForm: function(contactId, carId, carValues){
        carValues = carValues || {};
        let inputs = {};
        let errorNode = BX.create('div', {props: {className: 'ui-alert ui-alert-danger'}, style: {display: 'none'}});
        let dict = BX.Lukasheva.Garage.dictionary;
        let makeSelect = BX.create('select', {props: {className: 'ui-ctl-element'}});
        let modelSelect = BX.create('select', {props: {className: 'ui-ctl-element'}});   
        BX.Lukasheva.Garage.fillSelect(makeSelect, dict.makes, carValues.CARMAKE);
        BX.Lukasheva.Garage.fillSelect(modelSelect, dict.models[carValues.CARMAKE] || [], carValues.MODEL);

        BX.bind(makeSelect, 'change', function () {
            BX.Lukasheva.Garage.fillSelect(modelSelect, dict.models[this.value] || [], '');
        });

        inputs['CARMAKE'] = makeSelect;
        inputs['MODEL'] = modelSelect;   

        let selectRows = [
            BX.Lukasheva.Garage.createRow('CARS_TABLE_LABEL_CARMAKE', makeSelect, true),
            BX.Lukasheva.Garage.createRow('CARS_TABLE_LABEL_MODEL', modelSelect, true),
        ];

        let rows = this.fields.map(function (field) {
            inputs[field.code] = BX.create('input', {
                props: {className: 'ui-ctl-element', type: 'text', value: carValues[field.code] || ''},
                attrs: {name: field.code},
            });
            return BX.Lukasheva.Garage.createRow(field.label, inputs[field.code], false);
        });

        let form = BX.create('form', {
            children: [errorNode].concat(selectRows, rows),
            events: {submit: function (e) { e.preventDefault(); }},
        });

        let popup = BX.PopupWindowManager.create("car-popup-"+carId, null, {
            titleBar: BX.message(carId ? 'CARS_TABLE_TITLE_EDIT' : 'CARS_TABLE_TITLE_ADD'),
            content: form,
            width: 400, // ширина окна
            closeIcon: {
                // объект со стилями для иконки закрытия, при null - иконки не будет
                opacity: 1
            },
            
            closeByEsc: true, // закрытие окна по esc
            darkMode: false, // окно будет светлым или темным
            autoHide: false, // закрытие при клике вне окна
            draggable: true, // можно двигать или нет
            resizable: false, // можно ресайзить
            min_width: 100, // минимальная ширина окна
            lightShadow: true, // использовать светлую тень у окна
            angle: false, // появится уголок
            overlay: {
                // объект со стилями фона
                backgroundColor: 'black',
                opacity: 50
            }, 
            buttons: [
                new BX.PopupWindowButton({
                    text: BX.message('CARS_TABLE_ACTION_SAVE'), // текст кнопки
                    id: 'save-btn', // идентификатор
                    className: 'ui-btn ui-btn-success', // доп. классы
                    events: {
                        click: function() {
                            let _fields = {};
                            Object.keys(inputs).forEach(function(code) {
                                _fields[code] = inputs[code].value;
                            });
                            console.log(_fields);

                            BX.ajax.runComponentAction("lukasheva:cars.table","save", {
                                mode: "class", //это означает, что мы хотим вызывать действие из class.php
                                data: {
                                    fields: _fields,
                                    car_id: carId,
                                    contact_id: contactId
                                    }, 
                            }).then(function (response) {
                                //сюда будут приходить ответ, у которого status == 'success'
                                console.log('success '+response);
                                popup.close()

                                //получаем объект grid-a с авто по ID
                                let carGrid = BX.Main.gridManager.getInstanceById('CARS_GRID_ID');
                                if (carGrid) {
                                    //обновляем grid по адресу полученному из template.php
                                    carGrid.reload(BX.Lukasheva.Garage.gridUrl);
                                }
                            }, function (response) {
                                //сюда будут приходить все ответы, у которых status !== 'success'
                                console.log(response);
                                errorNode.textContent = response.errors[0].message;
                                errorNode.style.display = 'block';
                            });

                        }
                    }
                }),
                new BX.PopupWindowButton({
                    text: BX.message('CARS_TABLE_ACTION_CANCEL'),
                    id: 'decline-btn',
                    className: 'ui-btn ui-btn-danger',
                    events: {
                        click: function() {
                            this.popupWindow.close()
                        }
                    }
                })
            ],
            events: {
                onPopupClose: function() {
                    // Событие при закрытии окна   
                    this.destroy()             
                }
            }
        });
        popup.show();

    },
    fillSelect: function (select, items, selected) {
        BX.cleanNode(select);
        select.appendChild(BX.create('option', {
            props: {value: ''},
            text: BX.message('CARS_TABLE_SELECT_PLACEHOLDER'),
        }));
        items.forEach(function (item) {
            select.appendChild(BX.create('option', {
                props: {value: item, selected: item === selected},
                text: item,
            }));
        });
    },
    view: function(itemId) {
        //создает popup окно с данными определенной машины
        console.log('view '+itemId);
        BX.ajax.runComponentAction("lukasheva:cars.table","view", {
            mode: "class", 
            data: {car_id: itemId},  
            }).then(function (response) {
                //сюда будут приходить ответ, у которого status == 'success'
                console.log(response);
                var popup = BX.PopupWindowManager.create("car-view"+itemId, null, {
                    titleBar: response.data.title,
                    content: response.data.html,
                    width: 900, // ширина окна
                    zIndex: 100, // z-index
                    closeIcon: {
                        // объект со стилями для иконки закрытия, при null - иконки не будет
                        opacity: 1
                    },
                    
                    closeByEsc: true, // закрытие окна по esc
                    darkMode: false, // окно будет светлым или темным
                    autoHide: false, // закрытие при клике вне окна
                    draggable: true, // можно двигать или нет
                    resizable: false, // можно ресайзить
                    min_width: 100, // минимальная ширина окна
                    lightShadow: true, // использовать светлую тень у окна
                    angle: false, // появится уголок
                    overlay: {
                        // объект со стилями фона
                        backgroundColor: 'black',
                        opacity: 50
                    }, 
                    buttons: [
                        new BX.PopupWindowButton({
                            text: BX.message('CARS_TABLE_ACTION_CLOSE'),
                            id: 'decline-btn',
                            className: 'ui-btn ui-btn-primary',
                            events: {
                            click: function() {
                                this.popupWindow.close()
                            }
                            }
                        })
                    ],
                    events: {
                        onPopupClose: function() {
                            // Событие при закрытии окна   
                            this.destroy()             
                        }
                    }
                });

                popup.show();
            }, function (response) {
                //сюда будут приходить все ответы, у которых status !== 'success'
                console.log(response);
            });
        
    },
    edit: function(itemId){
         console.log('edit '+itemId);
    },
    add: function(contactId){

        console.log('add '+contactId);
        
        BX.Lukasheva.Garage.getmarksmodels(function () {
            BX.Lukasheva.Garage.openForm(contactId, 0, {});
        });
        
      
    },
    getmarksmodels: function(callback) {
        console.log('getmarksmoleks');
        BX.ajax.runComponentAction("lukasheva:cars.table","dictionary", {
            mode: "class", 
            data: {},  
            }).then(function (response) {
                //сюда будут приходить ответ, у которого status == 'success'
                console.log(response);
                BX.Lukasheva.Garage.dictionary = response.data;
                callback(response.data);
               
            });
    },
    newdeal: function(car_id) {
        console.log('newdeal '+car_id);
        BX.ajax.runComponentAction("lukasheva:cars.table","newdeal", {
            mode: "class", 
            data: {car_id: car_id},  
            }).then(function (response) {
                //сюда будут приходить ответ, у которого status == 'success'
                console.log(response);
                let dealId = response.data.id;
                BX.SidePanel.Instance.open('/crm/deal/details/' + dealId + '/');
               
            }, function (response) {
                //сюда будут приходить все ответы, у которых status !== 'success'
                console.log(response);
                BX.UI.Dialogs.MessageBox.alert(response.errors[0].message);
            });
    }
}