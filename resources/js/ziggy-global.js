import { Ziggy } from './ziggy';
import { route as ziggyRoute } from 'ziggy-js';

window.Ziggy = Ziggy;
window.route = (name, params = {}) => ziggyRoute(name, params, false, Ziggy);