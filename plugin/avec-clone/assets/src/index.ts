import './style.scss';
import { initListFilters } from './ts/list-filter';
import { initNavToggle } from './ts/nav-toggle';
import { initDatePickers } from './ts/date-picker';

document.addEventListener( 'DOMContentLoaded', () => {
	initListFilters();
	initNavToggle();
	initDatePickers();
} );
