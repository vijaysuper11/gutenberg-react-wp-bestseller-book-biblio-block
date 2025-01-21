<?php
if (!defined('ABSPATH')) exit;

// Register the dynamic Bestsellers block
function render_bestsellers_block($attributes) {
    $genre = isset($attributes['genre']) ? sanitize_text_field($attributes['genre']) : '';
    $title = isset($attributes['title']) ? sanitize_text_field($attributes['title']) : 'Bestsellers';

    if (!$genre) {
        return '<p>Please select a genre to display the bestsellers.</p>';
    }

    // Make a REST API request to the /books endpoint (defined in api.php)
    $response = wp_remote_get(rest_url("bestsellers/v1/books?catUri={$genre}"));

    if (is_wp_error($response)) {
        return '<p>Error fetching books for the selected genre.</p>';
    }

    $data = json_decode(wp_remote_retrieve_body($response), true);

    if (empty($data) || isset($data['message'])) {
        return '<p>No books found for the selected genre.</p>';
    }

    $books = $data; // Assuming the data returned contains the books directly
    ob_start();
    ?>
    <div class="bestsellers-block">
        <h3><?php echo esc_html($title); ?></h3>
        <div class="book-display">
            <?php foreach ($books as $book): ?>
                <?php
                $amazonLink = null;
                if (isset($book['affiliateLinks']) && is_array($book['affiliateLinks'])) {
                    foreach ($book['affiliateLinks'] as $link) {
                        if ($link['affiliateType'] === 'amazon') {
                            $amazonLink = $link['url'];
                            break;
                        }
                    }
                }
                $customURL = null;
                if (isset($book['seoFriendlyUrl']) && isset($book['isbn'])) {
                    $customURL= 'https://www.penguin.co.uk' . $book['seoFriendlyUrl'] .'/'. $book['isbn'];
                }
                //print_r('https://www.penguin.co.uk/' . $book['seoFriendlyUrl'] .'/'. $book['isbn']);
                ?>
                <div class="book-card">
                    <a href="<?php echo esc_url($customURL ?? '#'); ?>" target="_blank" rel="noopener noreferrer">
                        <img
                            src="<?php echo esc_url($book['coverUrls']['medium']['coverUrl'] ?? 'https://cdn.penguin.co.uk/dam-assets/books/9781529922936/9781529922936-jacket-medium.jpg'); ?>"
                            alt="<?php echo esc_attr($book['title'] ?? 'No Title'); ?>" />
                    </a>
                    <h4><?php echo esc_html($book['title'] ?? 'Unknown Title'); ?></h4>
                    <span class="author-name"><?php echo isset($book['authors']) && is_array($book['authors'])
                            ? esc_html(implode(', ', array_column($book['authors'], 'authorDisplay')))
                            : 'Unknown Author'; ?></span>
                    <?php if ($amazonLink): ?>
                        <a class="buy-now-button" href="<?php echo esc_url($amazonLink); ?>" target="_blank" rel="noopener noreferrer">
                        BUY FROM AMAZON
                        </a>
                        <div class="bottom">
                        <svg width="323" height="40" viewBox="0 0 323 40" fill="none" xmlns="http://www.w3.org/2000/svg" class="responsive-svg">
                            <mask id="mask0_0_1" style="mask-type:luminance" maskUnits="userSpaceOnUse" x="146" y="0" width="32" height="40">
                            <path fill-rule="evenodd" clip-rule="evenodd" d="M146.375 0H177.372V39.5825H146.375V0Z" fill="white"/>
                            </mask>
                            <g mask="url(#mask0_0_1)">
                            <path fill-rule="evenodd" clip-rule="evenodd" d="M161.836 3.16062e-05C146.996 0.0280068 146.369 16.8198 146.375 19.903C146.394 29.2602 150.594 39.6032 161.915 39.5824C173.233 39.5632 177.389 29.1417 177.372 19.785C177.365 16.7009 176.673 -0.0265334 161.836 3.16062e-05Z" fill="#1C1C1B"/>
                            </g>
                            <path fill-rule="evenodd" clip-rule="evenodd" d="M161.915 39.5556C151.163 39.5756 148.26 29.1988 148.241 20.3106C148.235 17.3814 147.692 1.02542 161.787 1.00003C175.882 0.973934 175.494 17.4157 175.504 20.3456C175.522 29.2374 172.671 39.5401 161.915 39.5556Z" fill="#1C1C1B"/>
                            <path fill-rule="evenodd" clip-rule="evenodd" d="M161.915 39.5556C151.163 39.5756 148.26 29.1988 148.241 20.3106C148.235 17.3814 147.692 1.02542 161.787 1.00003C175.882 0.973934 175.494 17.4157 175.504 20.3456C175.522 29.2374 172.671 39.5401 161.915 39.5556Z" fill="#FF6401"/>
                            <path fill-rule="evenodd" clip-rule="evenodd" d="M169.007 15.8361C167.745 14.0083 165.971 11.8507 165.773 9.63239C165.649 8.23858 165.74 6.68111 164.707 5.53269C164.405 5.19606 163.982 4.88856 163.561 4.69016C163.108 4.47716 162.531 4.34128 162.019 4.34128C161.539 4.34128 161.057 4.40922 160.599 4.535C160.055 4.6838 159.516 4.85964 158.951 4.93159C158.297 5.01433 157.771 4.98448 157.133 4.85589C156.753 4.77879 156.649 4.75176 156.269 4.67724C156.059 4.63585 155.49 4.48586 155.309 4.64688C154.88 5.03715 155.789 5.50873 156.062 5.68456C156.658 6.07669 156.904 6.20764 157.473 6.65079C158.239 7.25543 158.848 7.97505 159.083 8.87542C159.149 9.13471 159.172 9.39871 159.174 9.66835C159.231 13.5556 152.08 17.3724 152.178 24.2582C152.211 26.6939 153.644 27.0938 153.627 25.7439C153.602 24.1401 154.229 22.4078 154.88 20.9209C155.031 20.5774 155.512 20.474 155.404 21.0697C155.236 22.0124 154.809 23.6441 154.849 26.3041C154.856 26.9459 154.901 27.5933 155 28.2316C155.083 28.7408 155.207 29.2481 155.369 29.7458C155.507 30.1628 155.686 30.5646 155.908 30.9481C156.089 31.251 156.296 31.5407 156.541 31.8033C156.78 32.0619 157.037 32.2521 157.326 32.4573C157.493 32.5788 157.683 32.6837 157.764 32.8774C157.88 33.1555 157.484 33.2686 157.278 33.3549C157.05 33.4477 156.851 33.5518 156.636 33.6473C156.327 33.7832 156.02 33.9297 155.719 34.0794C155.535 34.1753 155.326 34.2315 155.147 34.3439C154.971 34.4473 154.881 34.6217 155.158 34.6589C155.282 34.6805 155.417 34.6483 155.547 34.6419C155.763 34.6358 155.995 34.6083 156.201 34.6805C156.482 34.7809 156.468 35.0245 156.478 35.2612C156.49 35.5188 156.684 35.6021 156.938 35.5395C157.226 35.4652 157.429 35.2621 157.654 35.0924C157.818 34.9673 157.999 34.838 158.189 34.7499C158.576 34.5747 159.036 34.6335 159.452 34.6516C159.681 34.6627 160.007 34.7515 160.194 34.5818C160.384 34.4064 160.246 34.1097 160.162 33.9186C160.084 33.735 159.986 33.5592 159.887 33.3847C159.84 33.3048 159.776 33.2336 159.737 33.151C159.577 32.8116 160.877 32.5565 161.064 32.5069C161.697 32.3539 162.359 32.217 163.009 32.1707C163.629 32.1211 164.311 32.1178 164.895 32.3374C165.146 32.4324 165.35 32.5941 165.516 32.7855C165.611 32.8901 165.827 33.207 165.635 33.3361C165.335 33.5354 165.022 33.708 164.733 33.9186C164.548 34.0481 164.37 34.184 164.213 34.3467C164.095 34.4652 164.063 34.6782 164.302 34.6648C164.618 34.6516 164.935 34.6358 165.248 34.6345C165.473 34.6335 165.717 34.6627 165.908 34.7788C166.217 34.9702 166.284 35.3787 166.634 35.5348C166.73 35.579 166.849 35.579 166.954 35.5501C167.183 35.4854 167.289 35.2701 167.412 35.102C167.526 34.9523 167.64 34.8016 167.822 34.7108C167.934 34.6551 168.062 34.6335 168.191 34.6335C168.421 34.6335 168.653 34.6373 168.883 34.6589C169.066 34.6805 169.314 34.7571 169.476 34.6455C169.78 34.4306 168.977 33.9522 168.816 33.8598C168.45 33.6529 167.927 33.4343 167.812 33.0069C167.681 32.5055 167.95 31.9829 168.184 31.5416C168.428 31.0733 168.76 30.6445 168.948 30.1518C169.127 29.7009 169.26 29.2312 169.335 28.7542C169.736 26.1504 169.069 23.3792 168.232 20.8933C168.131 20.5959 168.006 20.2995 167.906 20.0021C167.761 19.5324 168.263 19.4212 168.521 19.8771C168.766 20.3178 170.372 23.1198 170.842 25.202C171.138 26.5314 172.239 26.5841 172.207 24.2283C172.165 20.8129 170.747 18.3464 169.007 15.8361Z" fill="#1C1C1B"/>
                            <path fill-rule="evenodd" clip-rule="evenodd" d="M167.681 26.1146C167.875 27.8328 168.085 30.7971 165.773 30.8977C165.16 30.9243 164.398 30.5354 163.263 30.6048C161.338 30.7249 160.764 31.3944 159.191 31.1382C158.29 30.9931 157.625 30.5312 157.246 29.7396C156.816 28.8388 156.78 27.8185 156.711 26.8511C156.638 25.7711 156.638 24.6819 156.708 23.6008C156.761 22.8196 156.851 22.04 156.965 21.2626C157.153 19.9731 157.455 18.7008 157.779 17.4352C158.063 16.3201 158.361 15.2018 158.892 14.154C159.281 13.39 159.859 12.6535 160.717 12.2637C161.651 11.8398 162.959 11.8086 163.878 12.235C165.959 13.2036 165.908 15.4574 165.974 17.2887C166.027 18.6995 166.219 20.1488 166.501 21.5363C166.809 23.0782 167.501 24.5461 167.681 26.1146Z" fill="white"/>
                            <path fill-rule="evenodd" clip-rule="evenodd" d="M164.315 7.21737C164.741 7.67017 164.715 9.53981 164.016 10.1873C163.609 10.5669 162.917 10.6161 162.374 10.6863C161.771 10.7639 161.182 10.8843 160.587 11.0188C160.021 11.1441 160.06 10.601 160.605 10.2124C161.419 9.63337 163.462 9.37645 163.955 7.21737C163.972 7.13556 164.071 6.9588 164.315 7.21737Z" fill="white"/>
                            <path fill-rule="evenodd" clip-rule="evenodd" d="M161.353 7.70729C161.922 8.04392 162.736 7.82624 163.169 7.22064C163.603 6.61508 163.492 5.85222 162.92 5.51533C162.35 5.18055 161.534 5.39778 161.1 6.00245C160.668 6.60451 160.78 7.36948 161.353 7.70729Z" fill="white"/>
                            <path fill-rule="evenodd" clip-rule="evenodd" d="M161.89 6.5302C162.101 6.24595 162.478 6.14182 162.73 6.29652C162.977 6.45168 163.007 6.80616 162.793 7.09134C162.58 7.37462 162.205 7.47921 161.956 7.32549C161.704 7.1694 161.677 6.81344 161.89 6.5302Z" fill="#1C1C1B"/>
                            <line x1="0.166584" y1="24.51" x2="119.15" y2="24.4999" stroke="black"/>
                            <line x1="203.85" y1="24.5" x2="322.833" y2="24.4981" stroke="black"/>
                        </svg>

                        </div>
                    <?php else: ?>
                        <p>No Amazon link available.</p>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
    <?php
    return ob_get_clean();
}

// Enqueue Editor and Front-End Styles
function bestsellers_block_enqueue_assets() {
    // Front-End Styles
    wp_enqueue_style(
        'bestsellers-block-style',
        plugins_url('src/style.css', __FILE__),
        [],
        '1.0.0'
    );

    // Editor Styles
    wp_enqueue_style(
        'bestsellers-block-editor-style',
        plugins_url('src/editor.css', __FILE__),
        ['wp-edit-blocks'],
        '1.0.0'
    );
}
add_action('enqueue_block_assets', 'bestsellers_block_enqueue_assets');

