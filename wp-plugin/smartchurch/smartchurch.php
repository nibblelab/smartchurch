<?php
/*
Plugin Name: Smartchurch
Plugin URI: https://www.nibblelab.com/wp-smartchurch
Description: Plugin de integração do wordpress com a plataforma Smartchurch
Version: 1.0
Author: Nibblelab Tecnologia LTDA
Author URI: https://www.nibblelab.com/
*/

function smartchurch_styles() 
{
    $plugin_url = plugin_dir_url(__FILE__);
    wp_enqueue_style('smartchurch-style', $plugin_url . 'style.css'); 
}

add_action('admin_enqueue_scripts', 'smartchurch_styles');

// menu de configuração no painel de admin 
add_action('admin_menu', 'smartchurch_menu');
function smartchurch_menu() {
    add_options_page('Integração SmartChurch', 'SmartChurch', 'manage_options', 'smartchurch-admin-menu', 'smartchurch_menu_options' );
    add_action('admin_init', 'register_smartchurch_menu_settings' );
}

function register_smartchurch_menu_settings() {
    register_setting('smartchurch_menu_settings', 'smartchurch_api_token');
    register_setting('smartchurch_menu_settings', 'smartchurch_api_mode');
    register_setting('smartchurch_menu_settings', 'smartchurch_contexto_tipo');
    register_setting('smartchurch_menu_settings', 'smartchurch_contexto_id');
    register_setting('smartchurch_menu_settings', 'smartchurch_serie_page');
    register_setting('smartchurch_menu_settings', 'smartchurch_mensagem_page');
}

function smartchurch_menu_options() {
    if (!current_user_can('manage_options'))  {
        wp_die(__('You do not have sufficient permissions to access this page.'));
    }
?>
<div class="wrap">
    <h1 class="smartchurch-title">SmartChurch</h1>

    <h2 class="smartchurch-subtitle">Configurações</h2>
    <form method="post" action="options.php">
        <?php settings_fields('smartchurch_menu_settings'); ?>
        <?php do_settings_sections('smartchurch_menu_settings'); ?>
        <table class="form-table">
            <tr valign="top">
                <th scope="row">Token</th>
                <td><textarea id="smartchurch_api_token" name="smartchurch_api_token" style="width: 100%;" rows="10" ><?php echo esc_attr(get_option('smartchurch_api_token')); ?></textarea></td>
            </tr>
            <tr valign="top">
                <th scope="row">Modo</th>
                <td>
                    <select name="smartchurch_api_mode" style="width: 100%;">
                        <option value="" >Escolher</option>
                        <option value="P" <?php echo (get_option('smartchurch_api_mode') == 'P') ? 'selected' : ''; ?>>Produção</option>
                        <option value="T" <?php echo (get_option('smartchurch_api_mode') == 'T') ? 'selected' : ''; ?>>Teste</option>
                    </select>
                </td>
            </tr>
            <tr valign="top">
                <th scope="row">Contexto?</th>
                <td>
                    <select name="smartchurch_contexto_tipo" style="width: 100%;">
                        <option value="" >Escolher</option>
                        <option value="igreja" <?php echo (get_option('smartchurch_contexto_tipo') == 'igreja') ? 'selected' : ''; ?>>Igreja</option>
                        <option value="federacao" <?php echo (get_option('smartchurch_contexto_tipo') == 'federacao') ? 'selected' : ''; ?>>Federação</option>
                        <option value="sinodal" <?php echo (get_option('smartchurch_contexto_tipo') == 'sinodal') ? 'selected' : ''; ?>>Sinodal</option>
                    </select>
                </td>
            </tr>
            <tr valign="top">
                <th scope="row">Id do Contexto</th>
                <td><input type="text" id="smartchurch_contexto_id" name="smartchurch_contexto_id" style="width: 100%;" value="<?php echo esc_attr(get_option('smartchurch_contexto_id')); ?>" /></td>
            </tr>
            <?php
            if(get_option('smartchurch_contexto_tipo') == 'igreja')
            {
                $paginas = get_pages(array(
                    'sort_column' => 'post_date'
                ));
            ?>
            <tr valign="top">
                <th scope="row">Página de Exibição de Série</th>
                <td>
                    <select name="smartchurch_serie_page" style="width: 100%;">
                        <option value="" >Escolher</option>
                        <?php
                        foreach($paginas as $pagina)
                        {
                        ?>
                        <option value="<?php echo $pagina->ID ?>" <?php echo (get_option('smartchurch_serie_page') == $pagina->ID) ? 'selected' : ''; ?>><?php echo $pagina->post_title ?></option>
                        <?php
                        }
                        ?>
                    </select>
                </td>
            </tr>
            <tr valign="top">
                <th scope="row">Página de Exibição de Mensagem</th>
                <td>
                    <select name="smartchurch_mensagem_page" style="width: 100%;">
                        <option value="" >Escolher</option>
                        <?php
                        foreach($paginas as $pagina)
                        {
                        ?>
                        <option value="<?php echo $pagina->ID ?>" <?php echo (get_option('smartchurch_mensagem_page') == $pagina->ID) ? 'selected' : ''; ?>><?php echo $pagina->post_title ?></option>
                        <?php
                        }
                        ?>
                    </select>
                </td>
            </tr>
            <?php
            }
            ?>
        </table>
        <p class="submit">
            <input name="submit" id="submit" class="button button-primary" value="Salvar Configuração" type="submit">
        </p>
    </form>
    
    <h2 class="smartchurch-subtitle">Shortcodes</h2>
    <p>
        <span class="smartchurch-shortcode">[smartchurh-inscricao url=""]</span>: 
        Insere o formulário de inscrição em evento na página ou post em que o shortcode for utilizado. 
        Utiliza como parâmetro, o link (URL) do evento fornecido pelo smartchurch.
    </p>
</div>
<?php
}

