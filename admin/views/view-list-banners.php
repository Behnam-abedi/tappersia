<?php
$add_new_url = admin_url('admin.php?page=your-awesome-banner');
?>
<div class="wrap">
    <h1 class="wp-heading-inline">All Double Banners</h1>
    <a href="<?php echo esc_url($add_new_url); ?>" class="page-title-action">Add New</a>

    <hr class="wp-header-end">

    <table class="wp-list-table widefat fixed striped table-view-list posts">
        <thead>
            <tr>
                <th scope="col" class="manage-column column-title column-primary"><span>Name</span></th>
                <th scope="col" class="manage-column"><span>Shortcode</span></th>
                <th scope="col" class="manage-column"><span>Status</span></th>
                <th scope="col" class="manage-column"><span>Date</span></th>
            </tr>
        </thead>

        <tbody id="the-list">
            <?php
            $banners = get_posts(['post_type' => 'yab_banner', 'posts_per_page' => -1, 'post_status' => 'publish']);

            if ($banners) :
                foreach ($banners as $banner_post) :
                    $banner_id = $banner_post->ID;
                    $banner_data = get_post_meta($banner_id, '_yab_banner_data', true);
                    $edit_url = admin_url('admin.php?page=your-awesome-banner&action=edit&banner_id=' . $banner_id);

                    $shortcode = '[doublebanner_fixed]';
                    if (isset($banner_data['displayMethod']) && $banner_data['displayMethod'] === 'Embeddable') {
                        $shortcode = '[doublebanner id="' . $banner_id . '"]';
                    }
                    
                    $status = isset($banner_data['isActive']) && $banner_data['isActive'] ? 'Active' : 'Inactive';
                    $status_class = $status === 'Active' ? 'green' : 'red';
            ?>
                    <tr class="iedit author-self level-0 post-<?php echo $banner_id; ?> type-yab_banner status-publish hentry">
                        <td class="title column-title has-row-actions column-primary">
                            <strong><a class="row-title" href="<?php echo esc_url($edit_url); ?>"><?php echo esc_html($banner_post->post_title); ?></a></strong>
                            <div class="row-actions">
                                <span class="edit"><a href="<?php echo esc_url($edit_url); ?>">Edit</a> | </span>
                                <span class="trash"><a href="<?php echo get_delete_post_link($banner_id, '', true); ?>" class="submitdelete">Trash</a></span>
                            </div>
                        </td>
                        <td><input type="text" readonly="readonly" value="<?php echo esc_attr($shortcode); ?>" class="large-text code"></td>
                        <td><span style="color: <?php echo esc_attr($status_class); ?>;">●</span> <?php echo esc_html($status); ?></td>
                        <td>Published<br><?php echo get_the_date('Y/m/d', $banner_id); ?></td>
                    </tr>
            <?php endforeach; else : ?>
                <tr class="no-items"><td class="colspanchange" colspan="4">No banners found.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>