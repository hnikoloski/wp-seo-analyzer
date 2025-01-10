import { registerBlockType } from '@wordpress/blocks';
import { useBlockProps } from '@wordpress/block-editor';
import { __ } from '@wordpress/i18n';
import metadata from './block.json';

const SeoAnalyzerEdit = ({ attributes }) => {
    const blockProps = useBlockProps({
        className: `align${attributes.align}`,
    });

    return (
        <div {...blockProps}>
            <div id="wp-seo-analyzer-block" className="wp-seo-analyzer-block"></div>
        </div>
    );
};

const SeoAnalyzerSave = ({ attributes }) => {
    const blockProps = useBlockProps.save({
        className: `align${attributes.align}`,
    });

    return (
        <div {...blockProps}>
            <div id="wp-seo-analyzer-block" className="wp-seo-analyzer-block"></div>
        </div>
    );
};

registerBlockType(metadata.name, {
    ...metadata,
    edit: SeoAnalyzerEdit,
    save: SeoAnalyzerSave,
});
