/**
 * @name		jQuery Countdown Plugin
 * @author		Martin Angelov
 * @version 	1.0
 * @url			http://tutorialzine.com/2011/12/countdown-jquery/
 * @license		MIT License
 */

 "use strict";

(function($){
	
	// Количество секунд в каждом временном отрезке
	var days	= 24*60*60,
		hours	= 60*60,
		minutes	= 60;
	
	// Создаем плагин
	$.fn.countdown = function(prop){
		
		var options = $.extend({
			callback	: function(){},
			timestamp	: 0,
			days_h		: 'Days',
			hours_h		: 'Hours',
			minutes_h	: 'Minutes',
			seconds_h	: 'Seconds',
			countdiv	: false,
		},prop);
		
		var left, d, h, m, s, positions, i = 0, wrap;

		// инициализируем плагин
		init(this, options);
		
		wrap = this;
		positions = wrap.find('.position');
		
		(function tick(){
			positions = wrap.find('.position');
			i++;
			// Осталось времени
			left = Math.floor((options.timestamp - (new Date())) / 1000);
			
			if(left < 0){
				left = 0;
			}
			// Осталось дней
			d = Math.floor(left / days);
			if(d <= '99') {
				updateDuo(false, 0, 1, d);
				left -= d*days;
				
				// Осталось часов
				h = Math.floor(left / hours);
				updateDuo(false, 2, 3, h);
				left -= h*hours;
				
				// Осталось минут
				m = Math.floor(left / minutes);
				updateDuo(false, 4, 5, m);
				left -= m*minutes;
				
				// Осталось секунд
				s = left;
				updateDuo(false, 6, 7, s);
			} else {
				if(i == 1) {
					positions.eq(0).before('<span class="position">\
						<span class="digit static">0</span>\
					</span>');

				}
				updateDuo(true, 1, 2, d);
				left -= d*days;
				// Осталось часов
				h = Math.floor(left / hours);
				updateDuo(false, 3, 4, h);
				left -= h*hours;
				
				// Осталось минут
				m = Math.floor(left / minutes);
				updateDuo(false, 5, 6, m);
				left -= m*minutes;
				
				// Осталось секунд
				s = left;
				updateDuo(false, 7, 8, s);
			}
			
			// Вызываем возвратную функцию пользователя
			options.callback(d, h, m, s);
			
			// Планируем следующий вызов данной функции через 1 секунду
			setTimeout(tick, 1000);
		})();
		
		// Данная функция обновляет две цифоровые позиции за один раз
		function updateDuo(h,minor,major,value){
			positions = wrap.find('.position');
			if(h == true) {
				switchDigit(positions.eq(0),Math.floor(value/100)%100);
			}
			switchDigit(positions.eq(minor),Math.floor(value/10)%10);
			switchDigit(positions.eq(major),value%10);
		}
		
		return this;
	};


	function init(elem, options){
		elem.addClass('countdownHolder');

		// Создаем разметку внутри контейнера
		$.each([options.days_h,options.hours_h,options.minutes_h,options.seconds_h],function(i){
			$('<span class="count'+this+'">').html(
        '<span class="num">\
          <span class="position">\
            <span class="digit static">0</span>\
          </span>\
          <span class="position">\
            <span class="digit static">0</span>\
          </span>\
        </span>\
				<span class="name">'+this+'</span>'
			).appendTo(elem);
			
			if(options.countdiv){
				elem.append('<span class="countDiv countDiv'+i+'"></span>');
			}
		});

	}

	// Создаем анимированный переход между двумя цифрами
	function switchDigit(position,number){
		
		var digit = position.find('.digit')
		
		if(digit.is(':animated')){
			return false;
		}
		
		if(position.data('digit') == number){
			// Мы уже вывели данную цифру
			return false;
		}
		
		position.data('digit', number);
		
		var replacement = $('<span>',{
			'class':'digit',
			css:{
				top:'-2.1em',
				opacity:0
			},
			html:number
		});
		
		// Класс .static добавляется, когда завершается анимация.
		// Выполнение идет более плавно.
		
		digit
			.before(replacement)
			.removeClass('static')
			.animate({top:'2.5em',opacity:0},'fast',function(){
				digit.remove();
			})

		replacement
			.delay(100)
			.animate({top:0,opacity:1},'fast',function(){
				replacement.addClass('static');
			});
	}
})(jQuery);