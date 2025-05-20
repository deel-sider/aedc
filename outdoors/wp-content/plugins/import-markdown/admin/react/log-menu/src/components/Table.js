const useState = wp.element.useState;
import Pagination from '../../../shared-components/pagination/Pagination';

const useMemo = wp.element.useMemo;
const {__} = wp.i18n;

let PageSize = 10;

const Chart = (props) => {

    //Pagination - START --------------------------------------------------------

    const [currentPage, setCurrentPage] = useState(1);

    const currentTableData = useMemo(() => {
        const firstPageIndex = (currentPage - 1) * PageSize;
        const lastPageIndex = firstPageIndex + PageSize;
        return props.data.slice(firstPageIndex, lastPageIndex);
    }, [currentPage, props.data]);

    //Pagination - END ----------------------------------------------------------

    function handleDataIcon(columnName) {

        return props.formData.sortingColumn === columnName ? props.formData.sortingOrder : '';

    }

    return (

        <div className="daimma-data-table-container">

            <table className="daimma-react-table__daimma-data-table daimma-react-table__daimma-data-table-dashboard-menu">
                <thead>
                <tr>
                    <th>
                        <button
                            className={'daimma-react-table__daimma-sorting-button'}
                            onClick={props.handleSortingChanges}
                            value={'log_id'}
                            data-icon={handleDataIcon('log_id')}
                        >{__('ID', 'import-markdown')}</button>
                    </th>
                    <th>
                        <button
                            className={'daimma-react-table__daimma-sorting-button'}
                            onClick={props.handleSortingChanges}
                            value={'date'}
                            data-icon={handleDataIcon('date')}
                        >{__('Date', 'import-markdown')}</button>
                    </th>
                    <th>
                        <button
                            className={'daimma-react-table__daimma-sorting-button'}
                            onClick={props.handleSortingChanges}
                            value={'post_title'}
                            data-icon={handleDataIcon('post_title')}
                        >{__('Post', 'import-markdown')}</button>
                    </th>
                    <th>
                        <button
                            className={'daimma-react-table__daimma-sorting-button'}
                            onClick={props.handleSortingChanges}
                            value={'file_name'}
                            data-icon={handleDataIcon('file_name')}
                        >{__('File Name', 'import-markdown')}</button>
                    </th>
                </tr>
                </thead>
                <tbody>

                {currentTableData.map((row) => (
                    <tr key={row.log_id}>
                        <td>{row.log_id}</td>
                        <td>{row.formatted_date}</td>
                        <td>
                            <div className={'daimma-react-table__post-cell-container'}>
                                <a href={row.post_permalink}>
                                    {row.post_title}
                                </a>
                                <a href={row.post_permalink} target={'_blank'}
                                   className={'daimma-react-table__icon-link'}></a>
                                <a href={row.post_edit_link} className={'daimma-react-table__icon-link'}></a>
                            </div>
                        </td>
                        <td>{row.file_name}</td>
                    </tr>
                ))}

                </tbody>
            </table>

            {props.data.length === 0 && <div
                className="daimma-no-data-found">{__('We couldn\'t find any results matching your filters. Try adjusting your criteria.', 'import-markdown')}</div>}
            {props.data.length > 0 &&
                <div className="daimma-react-table__pagination-container">
                    <div className='daext-displaying-num'>{props.data.length + ' items'}</div>
                    <Pagination
                        className="pagination-bar"
                        currentPage={currentPage}
                        totalCount={props.data.length}
                        pageSize={PageSize}
                        onPageChange={page => setCurrentPage(page)}
                    />
                </div>
            }

        </div>

    );

};

export default Chart;
