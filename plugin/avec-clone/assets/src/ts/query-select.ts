/**
 * Wires up any `[data-avec-query-select]` — a plain `<select>` that, on change, reloads the
 * current page with its value written into the named query param (other params, like the
 * Comissões period filter's `inicio`/`fim`, are preserved). Used for the "Filtrar por recibo"
 * dropdown, which the real app applies immediately without a separate submit button.
 *
 * @param root Element to search within for `[data-avec-query-select]` selects. Defaults to `document`.
 */
export function initQuerySelects( root: ParentNode = document ): void {
	root.querySelectorAll< HTMLSelectElement >(
		'[data-avec-query-select]'
	).forEach( ( select ) => {
		const param = select.dataset.avecQuerySelect;
		if ( ! param ) {
			return;
		}

		select.addEventListener( 'change', () => {
			const url = new URL( window.location.href );
			url.searchParams.set( param, select.value );
			window.location.href = url.toString();
		} );
	} );
}
