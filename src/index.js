import { registerBlockType } from '@wordpress/blocks';
import { __ } from '@wordpress/i18n';
import { useEffect, useState, memo, useCallback } from '@wordpress/element';
import { 
    TextControl, 
    SelectControl, 
    Button, 
    CheckboxControl,
    Spinner 
} from '@wordpress/components';
import { useBlockProps } from '@wordpress/block-editor';
import { useTable, useSortBy, usePagination } from 'react-table';
import { useMemo } from '@wordpress/element';
import './style.scss';

// Memoized table component to prevent unnecessary re-renders
const Table = memo(({ data, pageSize: initialPageSize }) => {
    const [pageSize, setPageSize] = useState(initialPageSize);

    const columns = useMemo(() => [
        {
            Header: __('Title', 'wp-seo-analyzer'),
            accessor: 'title',
            Cell: ({ row }) => (
                <a href={row.original.edit_url} target="_blank" rel="noopener noreferrer">
                    {row.original.title}
                </a>
            )
        },
        {
            Header: __('Type', 'wp-seo-analyzer'),
            accessor: 'type'
        },
        {
            Header: __('Keyword Count', 'wp-seo-analyzer'),
            accessor: 'keyword_count',
            Cell: ({ value }) => value || '0'
        },
        {
            Header: __('Keyword Density (%)', 'wp-seo-analyzer'),
            accessor: 'keyword_density',
            Cell: ({ value }) => `${value}%`
        },
        {
            Header: __('Word Count', 'wp-seo-analyzer'),
            accessor: 'word_count'
        }
    ], []);

    const {
        getTableProps,
        getTableBodyProps,
        headerGroups,
        page,
        prepareRow,
        canPreviousPage,
        canNextPage,
        pageOptions,
        nextPage,
        previousPage,
        setPageSize: setTablePageSize,
        state: { pageIndex }
    } = useTable(
        {
            columns,
            data: data || [],
            initialState: { 
                pageSize,
                pageIndex: 0 
            }
        },
        useSortBy,
        usePagination
    );

    useEffect(() => {
        setTablePageSize(pageSize);
    }, [pageSize, setTablePageSize]);

    if (!data?.length) return null;

    return (
        <div className="wp-seo-analyzer__results">
            <div className="wp-seo-analyzer__results-header">
                <div className="wp-seo-analyzer__results-info">
                    <h2>{__('Results', 'wp-seo-analyzer')}</h2>
                    <span className="wp-seo-analyzer__total-count">
                        {__('Total items:', 'wp-seo-analyzer')} {data.length}
                    </span>
                </div>
                <SelectControl
                    label={__('Results per page', 'wp-seo-analyzer')}
                    value={pageSize}
                    options={[
                        { label: '10', value: 10 },
                        { label: '25', value: 25 },
                        { label: '50', value: 50 },
                        { label: '100', value: 100 }
                    ]}
                    onChange={value => setPageSize(Number(value))}
                />
            </div>
            <div className="wp-seo-analyzer__table-section">
                <div className="table-wrapper">
                    <table {...getTableProps()} className="wp-list-table widefat fixed striped">
                        <thead>
                            {headerGroups.map(headerGroup => (
                                <tr {...headerGroup.getHeaderGroupProps()}>
                                    {headerGroup.headers.map(column => (
                                        <th {...column.getHeaderProps(column.getSortByToggleProps())}>
                                            {column.render('Header')}
                                            <span>
                                                {column.isSorted
                                                    ? column.isSortedDesc
                                                        ? ' 🔽'
                                                        : ' 🔼'
                                                    : ''}
                                            </span>
                                        </th>
                                    ))}
                                </tr>
                            ))}
                        </thead>
                        <tbody {...getTableBodyProps()}>
                            {page.map(row => {
                                prepareRow(row);
                                return (
                                    <tr {...row.getRowProps()}>
                                        {row.cells.map(cell => (
                                            <td {...cell.getCellProps()}>
                                                {cell.render('Cell')}
                                            </td>
                                        ))}
                                    </tr>
                                );
                            })}
                        </tbody>
                    </table>
                </div>
                <div className="wp-seo-analyzer__pagination">
                    <Button
                        onClick={() => previousPage()}
                        disabled={!canPreviousPage}
                    >
                        {__('Previous', 'wp-seo-analyzer')}
                    </Button>
                    <span className="page-info">
                        {__('Page', 'wp-seo-analyzer')} <strong>{pageIndex + 1}</strong> {__('of', 'wp-seo-analyzer')} <strong>{pageOptions.length}</strong>
                    </span>
                    <Button
                        onClick={() => nextPage()}
                        disabled={!canNextPage}
                    >
                        {__('Next', 'wp-seo-analyzer')}
                    </Button>
                </div>
            </div>
        </div>
    );
});

// Main component
const SeoAnalyzer = memo(function SeoAnalyzer() {
    const [keyword, setKeyword] = useState('');
    const [postType, setPostType] = useState('all');
    const [loading, setLoading] = useState(false);
    const [error, setError] = useState(null);
    const [data, setData] = useState([]);
    const [showOnlyWithKeyword, setShowOnlyWithKeyword] = useState(false);
    const [postTypes, setPostTypes] = useState([
        { label: __('All', 'wp-seo-analyzer'), value: 'all' },
        { label: __('Posts', 'wp-seo-analyzer'), value: 'post' },
        { label: __('Pages', 'wp-seo-analyzer'), value: 'page' }
    ]);

    const fetchPostTypes = useCallback(async () => {
        try {
            const response = await fetch(`${wpSeoAnalyzer.apiUrl}/post-types`, {
                headers: {
                    'X-WP-Nonce': wpSeoAnalyzer.nonce
                }
            });
            if (!response.ok) throw new Error(__('Failed to fetch post types', 'wp-seo-analyzer'));
            const types = await response.json();
            setPostTypes([
                { label: __('All', 'wp-seo-analyzer'), value: 'all' },
                ...types.map(type => ({
                    label: type.label,
                    value: type.name
                }))
            ]);
        } catch (err) {
            console.error(err);
        }
    }, []);

    useEffect(() => {
        fetchPostTypes();
    }, [fetchPostTypes]);

    const handleSubmit = useCallback(async (event) => {
        event.preventDefault();
        if (!keyword.trim()) {
            setError(__('Please enter a keyword', 'wp-seo-analyzer'));
            return;
        }

        setLoading(true);
        setError(null);
        
        try {
            const response = await fetch(
                `${wpSeoAnalyzer.apiUrl}/analyze?keyword=${encodeURIComponent(keyword)}&post_type=${postType}&show_only_keyword=${showOnlyWithKeyword}`,
                {
                    headers: {
                        'X-WP-Nonce': wpSeoAnalyzer.nonce
                    }
                }
            );
            
            if (!response.ok) {
                throw new Error(__('Failed to fetch data', 'wp-seo-analyzer'));
            }
            
            const result = await response.json();
            setData(result);
        } catch (err) {
            setError(err.message);
        } finally {
            setLoading(false);
        }
    }, [keyword, postType, showOnlyWithKeyword]);

    const filteredData = useMemo(() => {
        if (!showOnlyWithKeyword) return data;
        return data.filter(item => item.keyword_count > 0);
    }, [data, showOnlyWithKeyword]);

    return (
        <div className="wp-seo-analyzer">
            <div className="wp-seo-analyzer__controls">
                <form onSubmit={handleSubmit}>
                    <TextControl
                        label={__('Keyword', 'wp-seo-analyzer')}
                        value={keyword}
                        onChange={setKeyword}
                        placeholder={__('Enter keyword to analyze', 'wp-seo-analyzer')}
                    />
                    <SelectControl
                        label={__('Post Type', 'wp-seo-analyzer')}
                        value={postType}
                        options={postTypes}
                        onChange={setPostType}
                    />
                    <Button
                        isPrimary
                        type="submit"
                        disabled={loading || !keyword.trim()}
                    >
                        {loading ? (
                            <>
                                <Spinner />
                                {__('Analyzing...', 'wp-seo-analyzer')}
                            </>
                        ) : (
                            __('Analyze', 'wp-seo-analyzer')
                        )}
                    </Button>
                    <CheckboxControl
                        label={__('Show only content with keyword', 'wp-seo-analyzer')}
                        checked={showOnlyWithKeyword}
                        onChange={setShowOnlyWithKeyword}
                    />
                </form>
            </div>
            {error && (
                <div className="wp-seo-analyzer__error">
                    {error}
                </div>
            )}
            {data && <Table data={filteredData} pageSize={10} />}
        </div>
    );
});

// Block edit component
const Edit = memo(function Edit() {
    const blockProps = useBlockProps();
    return (
        <div {...blockProps}>
            <SeoAnalyzer />
        </div>
    );
});

// Register the block
registerBlockType('wp-seo-analyzer/seo-analyzer', {
    edit: Edit
});

// Initialize the component in all possible contexts
document.addEventListener('DOMContentLoaded', () => {
    const containers = document.querySelectorAll('.wp-seo-analyzer-content');
    if (!containers.length) return;

    containers.forEach(container => {
        const root = wp.element.createRoot(container);
        root.render(<SeoAnalyzer />);
    });
});
