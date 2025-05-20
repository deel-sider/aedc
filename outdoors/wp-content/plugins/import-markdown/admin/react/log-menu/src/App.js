import Table from './components/Table';
import RefreshIcon from '../../../assets/img/icons/refresh-cw-01.svg';
import LoadingScreen from "../../shared-components/LoadingScreen";

const useState = wp.element.useState;
const useEffect = wp.element.useEffect;

const {__} = wp.i18n;

const App = () => {

    const [formData, setFormData] = useState(
        {
            optimizationStatus: 0,
            searchString: '',
            searchStringChanged: false,
            sortingColumn: 'log_id',
            sortingOrder: 'desc'
        }
    );

    const [dataAreLoading, setDataAreLoading] = useState(true);

    const [dataUpdateRequired, setDataUpdateRequired] = useState(false);

    const [tableData, setTableData] = useState([]);
    const [statistics, setStatistics] = useState({
        allRecords: 0,
    });

    useEffect(() => {

        setDataAreLoading(true);

        /**
         * Initialize the chart data with the data received from the REST API
         * endpoint provided by the plugin.
         */
        wp.apiFetch({
            path: '/daext-import-markdown/v1/log',
            method: 'POST',
            data: {
                search_string: formData.searchString,
                sorting_column: formData.sortingColumn,
                sorting_order: formData.sortingOrder,
                data_update_required: dataUpdateRequired
            }
        }).then(data => {

            // Set the table data with setTableData().
            setTableData(data.table);

            // Set the statistics.
            setStatistics({
                allRecords: data.statistics.all_records,
            });

            if (dataUpdateRequired) {

                // Set the dataUpdateRequired state to false.
                setDataUpdateRequired(false);

                // Set the form data to the initial state.
                setFormData({
                    searchString: '',
                    searchStringChanged: false,
                    sortingColumn: 'log_id',
                    sortingOrder: 'desc'
                });

            }

            setDataAreLoading(false);

        });

    }, [
        formData.searchStringChanged,
        formData.sortingColumn,
        formData.sortingOrder,
        dataUpdateRequired
    ]);

    /**
     * Function to handle key press events.
     *
     * @param event
     */
    function handleKeyUp(event) {

        // Check if Enter key is pressed (key code 13).
        if (event.key === 'Enter') {
            event.preventDefault(); // Prevent form submission.
            document.getElementById('daimma-search-button').click(); // Simulate click on search button.
        }

    }

    /**
     * Handle sorting changes.
     * @param e
     */
    function handleSortingChanges(e) {

        /**
         * Check if the sorting column is the same as the previous one.
         * If it is, change the sorting order.
         * If it is not, change the sorting column and set the sorting order to 'asc'.
         */
        let sortingOrder = formData.sortingOrder;
        if (formData.sortingColumn === e.target.value) {
            sortingOrder = formData.sortingOrder === 'asc' ? 'desc' : 'asc';
        }

        setFormData({
            ...formData,
            sortingColumn: e.target.value,
            sortingOrder: sortingOrder
        })

    }

    /**
     * Used to toggle the dataUpdateRequired value.
     * @param e
     */
    function handleDataUpdateRequired(e) {
        setDataUpdateRequired(prevDataUpdateRequired => {
            return !prevDataUpdateRequired;
        });
    }

    return (

        <>

            <React.StrictMode>

                {
                    !dataAreLoading ?

                        <div className="daimma-admin-body">

                            <div className={'daimma-react-table'}>

                                <div className={'daimma-react-table-header'}>
                                    <div className={'statistics'}>
                                        <div className={'statistic-label'}>{__('All records', 'import-markdown')}:</div>
                                        <div className={'statistic-value'}>{statistics.allRecords}</div>
                                    </div>
                                    <div className={'tools-actions'}>
                                        <button
                                            onClick={(event) => handleDataUpdateRequired(event)}
                                        ><img src={RefreshIcon} className={'button-icon'}></img>
                                            {__('Update metrics', 'import-markdown')}
                                        </button>
                                    </div>
                                </div>

                                <div
                                    className={'daimma-react-table__daimma-filters daimma-react-table__daimma-filters-dashboard-menu'}>

                                    <div className={'daimma-search-container'}>
                                        <input
                                            onKeyUp={handleKeyUp}
                                            type={'text'} placeholder={__('Filter by title', 'import-markdown')}
                                            value={formData.searchString}
                                            onChange={(event) => setFormData({
                                                ...formData,
                                                searchString: event.target.value
                                            })}
                                        />
                                        <input id={'daimma-search-button'} className={'daimma-btn daimma-btn-secondary'}
                                               type={'submit'} value={__('Search', 'import-markdown')}
                                               onClick={() => setFormData({
                                                   ...formData,
                                                   searchStringChanged: formData.searchStringChanged ? false : true
                                               })}
                                        />
                                    </div>

                                </div>

                                <Table
                                    data={tableData}
                                    handleSortingChanges={handleSortingChanges}
                                    formData={formData}
                                />

                            </div>

                        </div>

                        :
                        <LoadingScreen
                            loadingDataMessage={__('Loading data...', 'import-markdown')}
                            generatingDataMessage={__('Data is being generated. For large sites, this process may take several minutes. Please wait...', 'import-markdown')}
                            dataUpdateRequired={dataUpdateRequired}/>
                }

            </React.StrictMode>

        </>

    );

};
export default App;