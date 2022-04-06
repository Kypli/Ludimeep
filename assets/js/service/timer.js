import '../../styles/service/timer.css';

// Prochain mardi
var d_nextTuesday = new Date();
var twoDigitMonth = ((d_nextTuesday.getMonth().length + 1) === 1)
	? (d_nextTuesday.getMonth() + 1)
	: d_nextTuesday.getMonth() + 1 < 10
		? '0' + (d_nextTuesday.getMonth()+1)
		: (d_nextTuesday.getMonth()+1)
;
d_nextTuesday.setDate(d_nextTuesday.getDate() + (((2 + 7 - d_nextTuesday.getDay()) % 7) || 7));
d_nextTuesday.setHours(18, 30, 0, 0);

// Prochain mardi suivant
if (d_nextTuesday <= new Date('2022-04-12T18:30:00')){
	d_nextTuesday.setDate(d_nextTuesday.getDate() + 7);
}

// Date timer
var nextTuesday = d_nextTuesday.getFullYear() + '-' + twoDigitMonth + "-" + d_nextTuesday.getDate()
let d = new Date(nextTuesday + 'T19:30:00') - new Date();

const gc = s => document.querySelector('#countdown [count="' + s + '"] span');

const mainCalc = (s, t, c) => {
	gc(s).classList.remove('ping');
	const m = t % c;

	const e = a => gc(s)[a + 'Attribute'].bind(gc(s));

	e('set')('b', m < 10 ? '0' + m : m);
	setTimeout(() => gc(s).classList.add('ping'), 10);
	return m;
};

const count = (b = 0) => (d -= 1000) && count.seconds(d, b);

const opti = (v, n) => (v - v % n) / n;

count.seconds = (t, i = !1) => {
	t = opti(t, 1000);
	i && count.minutes(t, i);
	if (mainCalc('seconds', t, 60) == 59) count.minutes(t, i);
};

count.minutes = (t, i = !1) => {
	t = opti(t, 60);
	i && count.hours(t, i);
	if (mainCalc('minutes', t, 60) == 59) count.hours(t, i);
};

count.hours = (t, i = !1) => {
	t = opti(t, 60) - 1;
	i && count.days(t);
	if (mainCalc('hours', t, 24) == 23) count.days(t);
};

count.days = t => {
	t = opti(t, 24);
	gc('days').setAttribute('b', t < 10 ? '0' + t : t);
	setTimeout(() => gc('days').classList.add('ping'), 10);
};

setTimeout(() => {
	count(true);
	setInterval(count, 1000);
}, d % 1000);