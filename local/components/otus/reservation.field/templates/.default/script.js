BX.namespace('ReservationField'); // создали пространство имен для компонента

BX.ReservationField = {
    //создает popup окно с данными определенной процедуры, данные передаются в аргументах
    getReservationPopup: function (procedure_id, procedure_name, procedure_content, iblock_id) {
        var popup = BX.PopupWindowManager.create("popup-reservation-"+procedure_id, BX('element'), {
            content: procedure_content,
            width: 400, // ширина окна
            height: 260, // высота окна
            zIndex: 100, // z-index
            closeIcon: {
                // объект со стилями для иконки закрытия, при null - иконки не будет
                opacity: 1
            },
            titleBar: 'Запись на процедуру '+ procedure_name,
            closeByEsc: true, // закрытие окна по esc
            darkMode: false, // окно будет светлым или темным
            autoHide: false, // закрытие при клике вне окна
            draggable: true, // можно двигать или нет
            resizable: true, // можно ресайзить
            min_height: 100, // минимальная высота окна
            min_width: 100, // минимальная ширина окна
            lightShadow: true, // использовать светлую тень у окна
            angle: false, // появится уголок
            overlay: {
                // объект со стилями фона
                backgroundColor: 'black',
                opacity: 500
            }, 
            buttons: [
                new BX.PopupWindowButton({
                    text: 'Записаться', // текст кнопки
                    id: 'save-btn', // идентификатор
                    className: 'ui-btn ui-btn-success', // доп. классы
                    events: {
                      click: function() {
                            // Событие при клике на кнопку
                          let formId = document.getElementById('popup-window-content-popup-reservation-'+procedure_id);
                          let fio = formId.querySelector('.field-fio').value;
                          let datetime = formId.querySelector('.field-datetime').value;
                          

                          BX.ajax.runComponentAction("otus:reservation.field","add", {
                                mode: "class", //это означает, что мы хотим вызывать действие из class.php
                                data: {
                                    fields: {
                                        fio: fio,
                                        datetime: datetime,
                                        procedure_id: procedure_id,
                                        iblock_id: iblock_id
                                    }  
                                }
                            }).then(function (response) {
                                //сюда будут приходить ответ, у которого status == 'success'
                                console.log(response);
                                window.location = '/services/lists/'+iblock_id+'/view/0/';
                               	
                            }, function (response) {
                                //сюда будут приходить все ответы, у которых status !== 'success'
                                console.log(response);
                                		
                            });

                      }
                    }
                }),
                new BX.PopupWindowButton({
                    text: 'Отмена',
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
               onPopupShow: function() {
                  // Событие при показе окна
               },
               onPopupClose: function() {
                  // Событие при закрытии окна   
                   this.destroy()             
               }
            }
        });

        return popup;
    }
}
    


    
    BX.ready(function () {

        let procedures = document.querySelectorAll('.procedure-item-grid');

        if (procedures.length > 0)
        {
            //найдены элементы с классом procedure-item-grid, навешиваем событие по клику, которое будет открывать popup диалог
            for (i = 0; i < procedures.length; i++) {
                //проверяем заполнен ли у элемента атрибут с id, если да, то продолжаем
                if (procedures[i].hasAttribute('data-procedure-id')) {
                    //записывает значение атрибутов элемента в переменные
                    let procedure_id = procedures[i].getAttribute('data-procedure-id');
                    let procedure_name = procedures[i].getAttribute('data-procedure-name');
                    let fio = procedures[i].getAttribute('data-fio');
                    let iblock_id = procedures[i].getAttribute('data-iblock-id');
                                        
                    procedures[i].addEventListener('click', (event) => {
                        event.preventDefault();

                        let content = '<p><input class="field-fio" type="text" name="fio" value="'+fio+'" placeholder="ФИО пациента"></p>'
                            + '<p><input class="field-datetime" type="text" name="datetime" value="" onclick="BX.calendar({node: this, field: this, bTime: true});" placeholder="День и время"></p>';

                        BX.ReservationField.getReservationPopup(procedure_id, procedure_name, content, iblock_id).show();
                    });

                }               
            }
        }
    });