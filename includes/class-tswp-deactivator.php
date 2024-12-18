<?php
/**
 * Fired during plugin deactivation
 */
class TSWP_Deactivator {
    /**
     * Handles any necessary cleanup during deactivation
     */
    public static function deactivate() {
        // For now, we're not doing anything on deactivation
        // Tables will remain until plugin is uninstalled
    }
}