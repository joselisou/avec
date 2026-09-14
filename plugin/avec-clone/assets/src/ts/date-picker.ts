const WEEKDAYS = [ 'Dom', 'Seg', 'Ter', 'Qua', 'Qui', 'Sex', 'Sáb' ];
const MONTHS = [
	'Janeiro',
	'Fevereiro',
	'Março',
	'Abril',
	'Maio',
	'Junho',
	'Julho',
	'Agosto',
	'Setembro',
	'Outubro',
	'Novembro',
	'Dezembro',
];

function pad( n: number ): string {
	return String( n ).padStart( 2, '0' );
}

function toIso( year: number, month: number, day: number ): string {
	return `${ year }-${ pad( month + 1 ) }-${ pad( day ) }`;
}

/**
 * Renders a month grid into `panel` for the given year/month, wiring each day button to
 * navigate to `baseUrl?data=YYYY-MM-DD` when clicked, and prev/next buttons to redraw the grid
 * for an adjacent month without leaving the page.
 *
 * @param panel       Container to render the calendar into.
 * @param year        Four-digit year of the month being shown.
 * @param month       Zero-based month index (0 = January) being shown.
 * @param selectedIso The currently selected date, "YYYY-MM-DD", highlighted if visible.
 * @param baseUrl     URL the day buttons navigate to, with `?data=` appended.
 */
function renderCalendar(
	panel: HTMLElement,
	year: number,
	month: number,
	selectedIso: string,
	baseUrl: string
): void {
	panel.innerHTML = '';

	const header = document.createElement( 'div' );
	header.className = 'avec-clone-datepicker__nav';

	const prev = document.createElement( 'button' );
	prev.type = 'button';
	prev.textContent = '‹';
	prev.addEventListener( 'click', () =>
		renderCalendar(
			panel,
			month === 0 ? year - 1 : year,
			month === 0 ? 11 : month - 1,
			selectedIso,
			baseUrl
		)
	);

	const label = document.createElement( 'span' );
	label.textContent = `${ MONTHS[ month ] } ${ year }`;

	const next = document.createElement( 'button' );
	next.type = 'button';
	next.textContent = '›';
	next.addEventListener( 'click', () =>
		renderCalendar(
			panel,
			month === 11 ? year + 1 : year,
			month === 11 ? 0 : month + 1,
			selectedIso,
			baseUrl
		)
	);

	header.append( prev, label, next );
	panel.appendChild( header );

	const grid = document.createElement( 'div' );
	grid.className = 'avec-clone-datepicker__grid';

	WEEKDAYS.forEach( ( day ) => {
		const cell = document.createElement( 'span' );
		cell.className = 'avec-clone-datepicker__weekday';
		cell.textContent = day;
		grid.appendChild( cell );
	} );

	const firstOfMonth = new Date( Date.UTC( year, month, 1 ) );
	const startOffset = firstOfMonth.getUTCDay();
	const daysInMonth = new Date( Date.UTC( year, month + 1, 0 ) ).getUTCDate();

	for ( let i = 0; i < startOffset; i++ ) {
		grid.appendChild( document.createElement( 'span' ) );
	}

	for ( let day = 1; day <= daysInMonth; day++ ) {
		const iso = toIso( year, month, day );
		const button = document.createElement( 'button' );
		button.type = 'button';
		button.textContent = String( day );
		button.className = 'avec-clone-datepicker__day';
		if ( iso === selectedIso ) {
			button.classList.add( 'is-selected' );
		}
		button.addEventListener( 'click', () => {
			window.location.href = `${ baseUrl }?data=${ iso }`;
		} );
		grid.appendChild( button );
	}

	panel.appendChild( grid );
}

/**
 * Wires up every `[data-avec-datepicker]` toggle button to open a custom month-grid calendar.
 *
 * @param root Element to search within for `[data-avec-datepicker]` containers. Defaults to `document`.
 */
export function initDatePickers( root: ParentNode = document ): void {
	root.querySelectorAll< HTMLElement >( '[data-avec-datepicker]' ).forEach(
		( container ) => {
			const toggle = container.querySelector< HTMLButtonElement >(
				'.avec-clone-datepicker__toggle'
			);
			const panel = container.querySelector< HTMLElement >(
				'.avec-clone-datepicker__panel'
			);
			const selectedIso = container.dataset.date || '';
			const baseUrl = container.dataset.baseUrl || '';

			if ( ! toggle || ! panel || ! selectedIso ) {
				return;
			}

			const [ year, month ] = selectedIso.split( '-' ).map( Number );

			toggle.addEventListener( 'click', ( event ) => {
				event.stopPropagation();
				const isHidden = panel.hasAttribute( 'hidden' );
				if ( isHidden ) {
					renderCalendar(
						panel,
						year,
						month - 1,
						selectedIso,
						baseUrl
					);
					panel.removeAttribute( 'hidden' );
				} else {
					panel.setAttribute( 'hidden', '' );
				}
			} );

			document.addEventListener( 'click', ( event ) => {
				if ( ! container.contains( event.target as Node ) ) {
					panel.setAttribute( 'hidden', '' );
				}
			} );
		}
	);
}
