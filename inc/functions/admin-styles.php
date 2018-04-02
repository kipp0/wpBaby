<?php

/**
 * Runs on 'admin_init'
 */
function gp_action_admin_init() {
    // use editor-style.css for content editor
    add_editor_style();
}

add_action('admin_init', 'gp_action_admin_init');
