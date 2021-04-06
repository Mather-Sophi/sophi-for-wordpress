/* eslint-disable */
// Sophi Data and Settings
try {
	window.sophi = SOPHIDATA;
} catch (e) {}

(function (f, g) {
    window.sophi = window.sophi || {};
    var c = window.sophi;
    c.q = c.q || [];
	c.sendEvent = function (a) {
		c.q.push(a);
	};
	c.data = c.data || {};
	c.settings = c.settings || {};
	const b = c.settings;
	b.trackerName = b.trackerName || 'sophiTag';
	let a;
	try {
		window.localStorage &&
			(((a = localStorage.getItem(`${b.trackerName}.tagCdn`)) &&
				typeof a === 'string' &&
				a.length > 7) ||
				(a = void 0));
	} catch (e) {
		a = void 0;
	}
	b.loadFrom = a ? `${a}sophi.min.js` : b.loadFrom || 'https://cdn.sophi.io/latest/sophi.min.js';
	b.legacy = a
		? `${a}sophi.legacy.min.js`
		: b.loadFrom || 'https://cdn.sophi.io/latest/sophi.legacy.min.js';
	try {
		eval('let id = Symbol("id"), a = [...new Set([0,1])].includes(0);');
	} catch (e) {
		b.loadFrom = b.legacy;
	} finally {
		if (!window[b.trackerName]) {
			a = document.createElement('script');
			const d = document.getElementsByTagName('script')[0];
			a.async = 1;
			a.src = b.loadFrom;
			d.parentNode.insertBefore(a, d);
		}
	}
	c.sendEvent({ type: 'page_view' });
})();
