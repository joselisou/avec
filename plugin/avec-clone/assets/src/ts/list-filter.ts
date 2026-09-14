/**
 * Progressive enhancement: injects a text filter above each `.avec-clone-list` that shows/hides
 * `.avec-clone-card` items by matching their visible text. Sections still work fully without JS
 * (server-rendered lists), this just makes long lists (Comandas, Clientes) faster to scan.
 *
 * @param root Element to search within for `.avec-clone-list` elements. Defaults to `document`.
 */
export function initListFilters( root: ParentNode = document ): void {
	root.querySelectorAll< HTMLUListElement >( '.avec-clone-list' ).forEach(
		( list ) => {
			if (
				list.dataset.avecFilterAttached === 'true' ||
				'avecNoInstantFilter' in list.dataset
			) {
				return;
			}
			list.dataset.avecFilterAttached = 'true';

			const input = document.createElement( 'input' );
			input.type = 'search';
			input.className = 'avec-clone-filter';
			input.placeholder = 'Filtrar...';
			input.setAttribute( 'aria-label', 'Filtrar itens da lista' );

			list.parentElement?.insertBefore( input, list );

			const cards = Array.from(
				list.querySelectorAll< HTMLLIElement >( '.avec-clone-card' )
			);

			input.addEventListener( 'input', () => {
				const query = input.value.trim().toLowerCase();
				cards.forEach( ( card ) => {
					const matches =
						query === '' ||
						( card.textContent ?? '' )
							.toLowerCase()
							.includes( query );
					card.hidden = ! matches;
				} );
			} );
		}
	);
}
