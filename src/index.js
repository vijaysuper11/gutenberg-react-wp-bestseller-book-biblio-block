import { registerBlockType } from '@wordpress/blocks';
import { InspectorControls, RichText } from '@wordpress/block-editor';
import { PanelBody, SelectControl, Spinner, TextControl } from '@wordpress/components';
import { useState, useEffect } from '@wordpress/element';
import '../src/editor.css';
import '../src/style.css';

registerBlockType('bestsellers/block', {
    title: 'Bestsellers Block',
    icon: 'book',
    category: 'widgets',
    attributes: {
        genre: { type: 'string', default: '' },
        title: { type: 'string', default: 'Bestsellers' },
    },
    edit: ({ attributes, setAttributes }) => {
        const { genre, title } = attributes;
        const [genres, setGenres] = useState([]);
        const [loading, setLoading] = useState(true);
        const [books, setBooks] = useState([]);
        const [booksLoading, setBooksLoading] = useState(false);
        const [searchTerm, setSearchTerm] = useState('');
        const [filteredGenres, setFilteredGenres] = useState([]);

        useEffect(() => {
            const fetchGenres = async () => {
                setLoading(true);
                try {
                    const response = await fetch('/wp-json/bestsellers/v1/genres');
                    if (!response.ok) throw new Error(`HTTP error! status: ${response.status}`);
                    const data = await response.json();
                    if (data.categories && Array.isArray(data.categories)) {
                        const genreList = data.categories.map(category => ({
                            label: category.description || 'Unknown Category',
                            value: category.catUri || '',
                        }));
                        setGenres(genreList);
                        setFilteredGenres(genreList); // Set the initial filtered genres to all genres
                    } else {
                        setGenres([]);
                        setFilteredGenres([]);
                    }
                } catch (error) {
                    console.error('Error fetching genres:', error);
                    setGenres([]);
                    setFilteredGenres([]);
                } finally {
                    setLoading(false);
                }
            };
            fetchGenres();
        }, []);

        useEffect(() => {
            if (!genre) {
                setBooks([]);
                return;
            }

            const fetchBooks = async () => {
                setBooksLoading(true);
                try {
                    const response = await fetch(`/wp-json/bestsellers/v1/books?catUri=${genre}`);
                    if (!response.ok) throw new Error(`HTTP error! status: ${response.status}`);
                    const data = await response.json();
                    if (Array.isArray(data) && data.length > 0) {
                        const formattedBooks = data.map(book => ({
                            title: book.title,
                            authors: book.authors.map(author => author.authorDisplay),
                            image: book.coverUrls?.medium?.coverUrl || 'fallback-image-url.jpg',
                            affiliateLinks: book.affiliateLinks,
                            seoFriendlyUrl: book.seoFriendlyUrl,
                            isbn: book.isbn,
                        }));
                        setBooks(formattedBooks);
                    } else {
                        setBooks([]);
                    }
                } catch (error) {
                    console.error('Error fetching books:', error);
                    setBooks([]);
                } finally {
                    setBooksLoading(false);
                }
            };

            fetchBooks();
        }, [genre]);

        // Ensure that the state is being updated correctly when typing
        const handleSearchChange = (event) => {
            console.log("Search value:", event);  // Debugging log
            const value = event || ''; // Ensure the value is not undefined

            setSearchTerm(value);

            // Filter genres based on the search term
            if (value.length >= 2) {
                const filtered = genres.filter((genre) =>
                    genre.label.toLowerCase().includes(value.toLowerCase())
                );
                setFilteredGenres(filtered);
            } else {
                setFilteredGenres(genres); // Reset to full list if search term is too short
            }
        };

        const handleSelectChange = (value) => {
            setAttributes({ genre: value });
        };

        return (
            <>
                <InspectorControls>
                    <PanelBody title="Settings">
                        {loading ? (
                            <Spinner />
                        ) : (
                            <>
                                <TextControl
                                    label="Search Genre"
                                    value={searchTerm}  // Ensure value is correctly bound
                                    onChange={handleSearchChange}  // Handler should update state
                                    placeholder="Search for genres..."
                                />
                                <SelectControl
                                    label="Select Genre"
                                    value={genre}
                                    options={[{ label: 'Select a genre', value: '' }, ...filteredGenres]}
                                    onChange={handleSelectChange}
                                />
                            </>
                        )}
                    </PanelBody>
                </InspectorControls>
                <div className="bestsellers-block">
                    <RichText
                        tagName="h3"
                        value={title}
                        onChange={(value) => setAttributes({ title: value })}
                        placeholder="Enter block title"
                    />
                    {booksLoading ? (
                        <Spinner />
                    ) : books.length > 0 ? (
                        <div className="book-display">
                            {books.map((book, index) => {
                                const seofriendlyUrl = book.seoFriendlyUrl || '';
                                const isbncode = book.isbn || '';
                                const customUrl = `https://www.penguin.co.uk/${seofriendlyUrl}/${isbncode}`;

                                return (
                                    <div key={index} className="book-card">
                                        <a
                                            href={customUrl}
                                            target="_blank"
                                            rel="noopener noreferrer"
                                        >
                                            <img
                                                src={book.image?.startsWith('https') ? book.image.split('https:')[1] : book.image || 'fallback-image-url.jpg'}
                                                alt={book.title || 'No Title Available'}
                                            />
                                        </a>

                                        <h4>{book.title || 'Unknown Title'}</h4>
                                        <span className='author-name'>{book.authors?.join(', ') || 'Unknown Author'}</span>

                                        <div className="affiliate-links">
                                            {book.affiliateLinks?.map((link, i) => {
                                                if (link.affiliateType === 'amazon') {
                                                    return (
                                                        <a
                                                            key={i}
                                                            href={link.url}
                                                            target="_blank"
                                                            rel="noopener noreferrer"
                                                            className="buy-now-button"
                                                        >
                                                            BUY FROM AMAZON
                                                        </a>
                                                        
                                                    );
                                                }
                                                return null;
                                            }) || <p>No purchase links available</p>}
                                        </div>
                                    </div>
                                );
                            })}
                        </div>
                    ) : (
                        <p>No books available. Please select a different genre.</p>
                    )}
                </div>
            </>
        );
    },
    save: ({ attributes }) => {
        const { genre, title } = attributes;

        return (
            <div className="bestsellers-block">
                <h3>{title}</h3>
                <div className="bestsellers-carousel" data-genre={genre}>
                    <p>Books will be dynamically loaded here based on the selected genre.</p>
                </div>
            </div>
        );
    },
});
