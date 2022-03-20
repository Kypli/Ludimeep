import '../../styles/service/timer.css';

const gc = s => document.querySelector('#countdown [count="' + s + '"] span');

let d = new Date('2022-04-19T00:18:30') - new Date();

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