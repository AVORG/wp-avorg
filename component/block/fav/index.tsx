namespace AvorgBlockFav {
    window.wp.blocks.registerBlockType('avorg/block-fav', {
        title: 'Favorite Toggle',
        icon: 'star-half',
        category: 'widgets',
        edit: (props: any) => {
            return <div className={props.className}>
                <span className="dashicons dashicons-star-empty" title={'Add to favorites'} />
                <span className="dashicons dashicons-star-filled" title={'Remove from favorites'} />
            </div>
        },
        save: (props: any) => {
            return <div className={props.className}>
                <span className="dashicons dashicons-star-empty" title={'Add to favorites'} />
                <span className="dashicons dashicons-star-filled" title={'Remove from favorites'} />
            </div>
        }
    });
}