const { __ } = wp.i18n;
const { useState, useEffect } = wp.element;
const { SelectControl } = wp.components;
const { InspectorControls } = wp.blockEditor;

const GenreSelect = () => {
    const [genres, setGenres] = useState([]);
    const [selectedGenre, setSelectedGenre] = useState('');

    useEffect(() => {
        wp.apiFetch({ path: '/bestsellers/v1/genres' })
            .then((response) => {
                setGenres(response.categories || []);
            })
            .catch(() => {
                setGenres([]);
            });
    }, []);

    const handleChange = (genre) => {
        setSelectedGenre(genre);
    };

    return (
        <InspectorControls>
            <SelectControl
                label={__('Select Genre', 'bestsellers')}
                value={selectedGenre}
                options={[
                    { label: __('Select a Genre', 'bestsellers'), value: '' },
                    ...genres.map((genre) => ({
                        label: genre.description,
                        value: genre.catUri,
                    })),
                ]}
                onChange={handleChange}
            />
        </InspectorControls>
    );
};

export default GenreSelect;
