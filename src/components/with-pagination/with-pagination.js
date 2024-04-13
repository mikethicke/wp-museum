

const withPagination = BaseComponent => props => {
	const {
		currentPage,
		totalPages,
		pagesToShow = 5,
		searchCallback,
		searchParams,
		...otherProps
	} = props;
	
	const doSearch = ( newPage ) => {
		searchParams.page = newPage;
		searchCallback( searchParams );
	}

	const PageList = () => {
		const pageItems = [];
		let startPage;
		let endPage;
		if ( totalPages > pagesToShow ) {
			if ( currentPage <= pagesToShow - 1 ) {
				startPage = 1;
				endPage = pagesToShow;
			}
			else if ( totalPages - currentPage + 1 >= pagesToShow ) {
				startPage = Math.max( 1, currentPage - Math.floor( pagesToShow / 2 ) );
				endPage = Math.min( startPage + pagesToShow - 1, totalPages );
			} else {
				startPage = totalPages - pagesToShow + 1;
				endPage = totalPages;
			}
		} else {
			startPage = 1;
			endPage = totalPages;
		}
		if ( startPage > 1 && totalPages > pagesToShow ) {
			pageItems.push(
				<li key = { -1 }>
					<a
						onClick = { () => doSearch( 1 ) }
					>
						{ 1 }
					</a>
				</li>
			);
		}
		if ( startPage > 2 && totalPages > pagesToShow ) {
			pageItems.push( 
				<li key = { 0 }>
					...
				</li>
			);
		}
		for ( let pageCounter = startPage; pageCounter <= endPage; pageCounter++ ) {
			pageItems.push (
				<li
					key = { pageCounter }
					className = { pageCounter == currentPage ? 
						'page-list-selected' : 'page-list-unselected'
					}
				>
					<a
						onClick = { () => doSearch( pageCounter ) }
					>
						{ pageCounter }
					</a>
				</li>
			);
		}
		if ( endPage < totalPages && totalPages > pagesToShow ) {
			pageItems.push( 
				<li key = { endPage + 1 }>
					...
				</li>
			);
			pageItems.push(
				<li key = { endPage + 2 }>
					<a
						onClick = { () => doSearch( totalPages ) }
					>
						{ totalPages }
					</a>
				</li>
			);
		}

		return (
			<ol>
				{ totalPages > pagesToShow &&
					<li>
						<a
							onClick = { () => doSearch( 1 ) }
						>
								<span className = 'pagination-symbol'>&laquo;</span>
						</a>
					</li>
				}
				{ totalPages > pagesToShow &&
					<li>
						<a
							onClick = { () => doSearch( currentPage - 1) }
						>
							<span className = 'pagination-symbol'>&lsaquo;</span>
						</a>
					</li>
				}
				{ pageItems }
				{ totalPages > pagesToShow &&
					<li>
						<a
							onClick = { () => doSearch( currentPage + 1 ) }
						>
							<span className = 'pagination-symbol'>&rsaquo;</span>
						</a>
					</li>
				}
				{ totalPages > pagesToShow &&
					<li>
						<a
							onClick = { () => doSearch( totalPages ) }
						>
							<span className = 'pagination-symbol'>&raquo;</span>
						</a>
					</li>
				}
			</ol>
		);
	} 

	return (
		<div className = 'paginated-component'>
			{ totalPages > 1 &&
				<div className = 'pagination'>
					<PageList />
				</div>
			}
			<BaseComponent { ...otherProps } />
			{ totalPages > 1 &&
				<div className = 'pagination'>
					<PageList />
				</div>
			}
		</div>
	);
}

export default withPagination;