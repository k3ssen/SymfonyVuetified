// any CSS you import will output into a single css file (app.css in this case)
import './styles/app.css';

import Vue from 'vue';

Vue.component('SvTextEditor', () => import('./components/SvTextEditor'));