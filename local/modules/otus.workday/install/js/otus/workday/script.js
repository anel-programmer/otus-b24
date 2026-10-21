BX.namespace('JsWorkday'); // создали пространство имен для модуля

BX.JsWorkday = {
    //создает popup окно 
    getWorkdayPopup: function (state) {
        let isStatus = false; // пишем Подолжить
        if (state === 'start')
            isStatus = true; // пишем Начать
        var popup = BX.PopupWindowManager.create("popup-workday-" + state, null, {
            content: BX.message(isStatus ? 'WORKDAY_CONTENT_START' : 'WORKDAY_CONTENT_CONTINUE'),
            width: 400, // ширина окна
            height: 200, // высота окна
            zIndex: 100, // z-index
            closeIcon: {
                // объект со стилями для иконки закрытия, при null - иконки не будет
                opacity: 1
            },
            titleBar: BX.message(isStatus ? 'WORKDAY_TITLE_START' : 'WORKDAY_TITLE_CONTINUE'),
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
                    text: BX.message(isStatus ? 'WORKDAY_BTN_START' : 'WORKDAY_BTN_CONTINUE'), // текст кнопки
                    id: 'start-btn', // идентификатор
                    className: 'ui-btn ui-btn-success', // доп. классы
                    events: {
                      click: function() {
                        // Событие при клике на кнопку
                        console.log('start day');  
                        BX.ajax.runAction('otus:workday.workday.start', { data: {} })
                        .then(function (response) {
                            console.log(response.data);
                            //если день открыт, то закрывает popup
                            if (response.data.state === 'OPENED') {
                                popup.close();
                                location.reload();
                                 
                            }
                            
                        }, function (response) {
                            //если ошибка запуска таймера, то показать сообщение
                            console.error(response);
                            var msg = response.errors && response.errors[0]
                                ? response.errors[0].message
                                : BX.message('WORKDAY_START_ERROR');
                            alert(msg);
                        });
                        
                      }
                    }
                }),
                new BX.PopupWindowButton({
                    text: BX.message('WORKDAY_BTN_CANCEL'),
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
    },
    //метод рисует своб кнопку вместо штатных
    renderButtons: function(state){
        let btnAction = document.querySelector('.tm-control-panel__actions-list');
        if (btnAction == undefined){
            return;
        }
        btnAction.style.display = 'none'; //скрываем штатную панель с кнопками
        // чтобы не дублировать при повторном срабатывании события
        var old = BX('workday-custom-actions');
        if (old) {
            BX.remove(old);
        }

        var mode = null;
        var text = '';

        if (state === 'CLOSED') {
            mode = 'start';
            text = BX.message('WORKDAY_BTN_START');   // Начать
        } else if (state === 'PAUSED') {
            mode = 'continue';
            text = BX.message('WORKDAY_BTN_CONTINUE');  // Продолжить 
        } else {
            // OPENED / EXPIRED: свою кнопку не показываем, возвращаем штатную панель
            btnAction.style.display = '';
            return;
        }

        var box = BX.create('div', {
            props: { id: 'workday-custom-actions', className: 'workday-custom-actions' },
            children: [
                BX.create('button', {
                    props: { className: 'ui-btn ui-btn-success', type: 'button' },
                    text: text,
                    events: {
                        click: function () {
                            BX.JsWorkday.getWorkdayPopup(mode).show();
                        }
                    }
                })
            ]
        });
        btnAction.parentNode.insertBefore(box, btnAction.nextSibling);

    }
    
}
    
BX.ready(function () {
    console.log('workday module');
    //событие при открытии окна с данными о пользователе и рабочем времени
    BX.addCustomEvent("onTimeManDataRecieved", function(params){
        console.log('onTimeManDataRecieved', params);

        // получаем статус, если PAUSED - то продолжаем день, если CLOSED - начинаем
        let state = (params && params.STATE);

        BX.JsWorkday.renderButtons(state);    

    });

});