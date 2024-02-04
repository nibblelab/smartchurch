package com.nibblelab.smartchurch.activity;

import android.content.Intent;

import com.google.android.material.navigation.NavigationView;
import androidx.fragment.app.Fragment;
import androidx.fragment.app.FragmentTransaction;
import androidx.core.view.GravityCompat;
import androidx.drawerlayout.widget.DrawerLayout;
import android.os.Bundle;

import android.view.Menu;
import android.view.MenuItem;
import android.view.View;
import android.widget.ImageView;
import android.widget.LinearLayout;
import android.widget.ProgressBar;
import android.widget.TextView;

import com.nibblelab.smartchurch.R;
import com.nibblelab.smartchurch.common.PermissionHelper;
import com.nibblelab.smartchurch.fragments.AgendaFragment;
import com.nibblelab.smartchurch.fragments.BookmarkFragment;
import com.nibblelab.smartchurch.fragments.EstudoFragment;
import com.nibblelab.smartchurch.fragments.HomeFragment;
import com.nibblelab.smartchurch.fragments.MembresiaFragment;
import com.nibblelab.smartchurch.fragments.OracaoFragment;
import com.nibblelab.smartchurch.fragments.PalavraFragment;
import com.nibblelab.smartchurch.fragments.PerfilFragment;
import com.nibblelab.smartchurch.fragments.SmartFragment;
import com.nibblelab.smartchurch.fragments.TransmissaoFragment;

public class SmartChurch extends Base
        implements NavigationView.OnNavigationItemSelectedListener,
                    HomeFragment.OnHomeFragInteractionListener,
                    PerfilFragment.OnPerfilFragInteractionListener,
                    MembresiaFragment.OnMembresiaFraInteractionListener,
                    PalavraFragment.OnPalavraFragInteractionListener,
                    EstudoFragment.OnEstudoFragInteractionListener,
                    TransmissaoFragment.OnTransmissaoFragInteractionListener,
                    AgendaFragment.OnAgendaFragInteractionListener,
                    SmartFragment.OnSmartFragInteractionListener,
                    BookmarkFragment.OnBookmarkFragInteractionListener,
                    OracaoFragment.OnOracaoFragInteractionListener {

    public static final String TAG = "SmartChurch";

    NavigationView navigationView;
    TextView nome;
    TextView email;
    Menu menu;
    MenuItem nav_palavra;
    MenuItem nav_estudo;
    MenuItem nav_transmissao;
    MenuItem nav_agenda;
    MenuItem nav_like;
    MenuItem nav_oracao;
    MenuItem nav_smart;

    // variáveis de transferência de dados entre fragmentos
    Fragment frag;
    Object transfer;

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_smartchurch);
        toolbar = findViewById(R.id.toolbar);
        this.setToolbarTitle(R.string.app_name);

        /* verifica permissões */
        PermissionHelper.checkCameraPermission(this);

        /* menu */
        DrawerLayout drawer = findViewById(R.id.drawer_layout);
        navigationView = findViewById(R.id.nav_view);
        /*ActionBarDrawerToggle toggle = new ActionBarDrawerToggle(
                this, drawer, toolbar, R.string.navigation_drawer_open, R.string.navigation_drawer_close);
        drawer.addDrawerListener(toggle);
        toggle.syncState();*/
        LinearLayout toggle = findViewById(R.id.menu_toggle);
        toggle.setOnClickListener(v -> drawer.openDrawer(GravityCompat.START));
        navigationView.setNavigationItemSelectedListener(this);

        /* loading */
        progress = (ProgressBar) findViewById(R.id.loading);
        this.initLoadingSpinner();

        /* menu rápido */
        LinearLayout fm_home = findViewById(R.id.fastmenu_home);
        fm_home.setOnClickListener(v -> SmartChurch.this.toInicio(true));
        LinearLayout fm_palavra = findViewById(R.id.fastmenu_palavra);
        fm_palavra.setOnClickListener(v -> SmartChurch.this.toPalavra(false));
        LinearLayout fm_perfil = findViewById(R.id.fastmenu_perfil);
        fm_perfil.setOnClickListener(v -> SmartChurch.this.toPerfil());
        LinearLayout fm_membro = findViewById(R.id.fastmenu_membro);
        fm_membro.setOnClickListener(v -> SmartChurch.this.toMembresia());
        LinearLayout fm_agenda = findViewById(R.id.fastmenu_agenda);
        fm_agenda.setOnClickListener(v -> SmartChurch.this.toAgenda());
        LinearLayout fm_smart = findViewById(R.id.fastmenu_smart);
        fm_smart.setOnClickListener(v -> SmartChurch.this.toSmart());
        toggleFastMenu(FastMenu.MENU_HOME);

        /* configurações */
        this.generateHeaderData();
        this.toInicio(false);

        menu = navigationView.getMenu();
        nav_palavra = menu.findItem(R.id.nav_palavra);
        nav_estudo = menu.findItem(R.id.nav_estudo);
        nav_transmissao = menu.findItem(R.id.nav_transmissao);
        nav_agenda = menu.findItem(R.id.nav_agenda);
        nav_like = menu.findItem(R.id.nav_like);
        nav_oracao = menu.findItem(R.id.nav_oracao);
        nav_smart = menu.findItem(R.id.nav_smart);

        if(!this.userHasIgrejaData() || !this.userHasModulo("MOD_IGREJA")) {
            // não tem informação de igreja e nem o módulo habilitado. Esconda os menus que dependem dessa informação
            nav_palavra.setVisible(false);
            nav_estudo.setVisible(false);
            nav_transmissao.setVisible(false);
            nav_like.setVisible(false);
            nav_oracao.setVisible(false);
            disableFastMenu(FastMenu.MENU_PALAVRA);
        }

        if(!this.userHasIgrejaData() || !this.userHasModulo("MOD_AGENDA")) {
            // não tem informação de igreja e nem o módulo de agenda. Esconda o menu de agenda
            nav_agenda.setVisible(false);
            disableFastMenu(FastMenu.MENU_AGENDA);
        }

        // smarcode não está ativo
        nav_smart.setVisible(false);
    }

    /**
     * Desabilite um elemento do menu rápido
     *
     * @param menu
     */
    private void disableFastMenu(int menu)
    {
        if(menu == FastMenu.MENU_PALAVRA) {
            FastMenu.MENUS[1].enabled = false;
            disableFastMenuView(FastMenu.MENUS[1].viewId);
            enableFastMenuView(FastMenu.MENUS[2].viewId);
            FastMenu.MENUS[2].enabled = true;
        }
        else if(menu == FastMenu.MENU_AGENDA) {
            FastMenu.MENUS[4].enabled = false;
            disableFastMenuView(FastMenu.MENUS[4].viewId);
            enableFastMenuView(FastMenu.MENUS[3].viewId);
            FastMenu.MENUS[3].enabled = true;
        }
    }

    /**
     * Desabilite uma view do menu rápido
     *
     * @param viewId
     */
    private void disableFastMenuView(int viewId)
    {
        LinearLayout view = findViewById(viewId);

        view.setVisibility(View.GONE);
    }

    /**
     * Habilite uma view do menu rápido
     *
     * @param viewId
     */
    private void enableFastMenuView(int viewId)
    {
        LinearLayout view = findViewById(viewId);

        view.setVisibility(View.VISIBLE);
    }

    /**
     * Ativa/Desativa um menu rápido
     *
     * @param menu
     */
    private void toggleFastMenu(int menu)
    {
        for(FastMenuItem i : FastMenu.MENUS) {
            if(!i.enabled) {
                continue;
            }
            if(menu == FastMenu.MENU_NONE) {
                markFastMenuAsInactive(i.viewId, i.imgId, i.txtId, i.POSITION);
                i.active = false;
                continue;
            }
            if(menu != i.MENU) {
                markFastMenuAsInactive(i.viewId, i.imgId, i.txtId, i.POSITION);
                i.active = false;
            }
            else {
                markFastMenuAsActive(i.viewId, i.imgId, i.txtId, i.POSITION);
                i.active = true;
            }
        }
    }

    /**
     * Aplica a ativação em menu rápido
     *
     * @param viewId
     * @param imgId
     * @param txtId
     * @param position
     */
    private void markFastMenuAsActive(int viewId, int imgId, int txtId, int position)
    {
        LinearLayout view = findViewById(viewId);
        ImageView img = findViewById(imgId);
        TextView txt = findViewById(txtId);

        if(position == FastMenu.POS_INNER) {
            view.setBackgroundResource(R.drawable.bg_border_active);
        }
        else if(position == FastMenu.POS_LEFT) {
            view.setBackgroundResource(R.drawable.bg_border_tl_radius_active);
        }
        else if(position == FastMenu.POS_RIGHT) {
            view.setBackgroundResource(R.drawable.bg_border_tr_radius_active);
        }
        img.setColorFilter(getResources().getColor(R.color.menuTxt));
        txt.setTextColor(getResources().getColor(R.color.menuTxt));
    }

    /**
     * Aplica a desativação em menu rápido
     *
     * @param viewId
     * @param imgId
     * @param txtId
     * @param position
     */
    private void markFastMenuAsInactive(int viewId, int imgId, int txtId, int position)
    {
        LinearLayout view = findViewById(viewId);
        ImageView img = findViewById(imgId);
        TextView txt = findViewById(txtId);

        if(position == FastMenu.POS_INNER) {
            view.setBackgroundResource(R.drawable.bg_border);
        }
        else if(position == FastMenu.POS_LEFT) {
            view.setBackgroundResource(R.drawable.bg_border_tl_radius);
        }
        else if(position == FastMenu.POS_RIGHT) {
            view.setBackgroundResource(R.drawable.bg_border_tr_radius);
        }
        img.setColorFilter(getResources().getColor(R.color.menuImgColor));
        txt.setTextColor(getResources().getColor(R.color.menuColor));
    }

    /**
     * Gere os dados do cabeçalho do menu
     */
    private void generateHeaderData()
    {
        View headerLayout = navigationView.getHeaderView(0);
        nome = (TextView) headerLayout.findViewById(R.id.menu_nome);
        email = (TextView) headerLayout.findViewById(R.id.menu_email);

        if(this.isUserLogged())
        {
            nome.setText(this.user.getNome());
            email.setText(this.user.getEmail());
        }
    }

    @Override
    public void onBackPressed() {
        DrawerLayout drawer = findViewById(R.id.drawer_layout);
        if (drawer.isDrawerOpen(GravityCompat.START)) {
            drawer.closeDrawer(GravityCompat.START);
        } else {
            super.onBackPressed();
        }
    }

    @Override
    public boolean onCreateOptionsMenu(Menu menu) {
        getMenuInflater().inflate(R.menu.menu_content, menu);
        return true;
    }

    @Override
    public boolean onOptionsItemSelected(MenuItem item) {
        return super.onOptionsItemSelected(item);
    }

    @SuppressWarnings("StatementWithEmptyBody")
    @Override
    public boolean onNavigationItemSelected(MenuItem item) {

        int id = item.getItemId();

        if (id == R.id.nav_home) {
            this.toInicio(true);
        } else if (id == R.id.nav_perfil) {
            this.toPerfil();
        } else if (id == R.id.nav_membro) {
            this.toMembresia();
        } else if (id == R.id.nav_palavra) {
            this.toPalavra(false);
        } else if (id == R.id.nav_estudo) {
            this.toEstudo(false);
        } else if (id == R.id.nav_transmissao) {
            this.toTransmissao();
        } else if (id == R.id.nav_agenda) {
            this.toAgenda();
        } else if (id == R.id.nav_like) {
            this.toBookmark();
        } else if (id == R.id.nav_oracao) {
            this.toPedidosDeOracao();
        } else if (id == R.id.nav_smart) {
            this.toSmart();
        } else if (id == R.id.nav_logout) {
            // faça o logout
            this.doLogout();
        }

        DrawerLayout drawer = findViewById(R.id.drawer_layout);
        drawer.closeDrawer(GravityCompat.START);
        return true;
    }

    public void toInicio(boolean replace)
    {
        Fragment home = new HomeFragment();
        FragmentTransaction ft = getSupportFragmentManager().beginTransaction();
        if(replace)
        {
            ft.replace(R.id.main_content, home, "home-frag").commit();
        }
        else
        {
            ft.add(R.id.main_content, home, "home-frag").commit();
        }
        toggleFastMenu(FastMenu.MENU_HOME);
    }

    public void toPerfil()
    {
        frag = new PerfilFragment();
        FragmentTransaction ft = getSupportFragmentManager().beginTransaction();
        ft.replace(R.id.main_content, frag, "perfil-frag").commit();
        toggleFastMenu(FastMenu.MENU_PERFIL);
    }

    public void toMembresia()
    {
        frag = new MembresiaFragment();
        FragmentTransaction ft = getSupportFragmentManager().beginTransaction();
        ft.replace(R.id.main_content, frag, "membresia-frag").commit();
        toggleFastMenu(FastMenu.MENU_MEMBRO);
    }

    public void toPalavra(boolean hasTransfer)
    {
        frag = new PalavraFragment();
        FragmentTransaction ft = getSupportFragmentManager().beginTransaction();
        ft.replace(R.id.main_content, frag, "palavra-frag").commit();
        toggleFastMenu(FastMenu.MENU_PALAVRA);
        ((PalavraFragment) frag).setHasTransfer(hasTransfer);
        if(!hasTransfer) {
            this.transfer = null;
        }
    }

    public void toEstudo(boolean hasTransfer)
    {
        frag = new EstudoFragment();
        FragmentTransaction ft = getSupportFragmentManager().beginTransaction();
        ft.replace(R.id.main_content, frag, "estudo-frag").commit();
        toggleFastMenu(FastMenu.MENU_NONE);
        ((EstudoFragment) frag).setHasTransfer(hasTransfer);
        if(!hasTransfer) {
            this.transfer = null;
        }
    }

    public void toTransmissao()
    {
        frag = new TransmissaoFragment();
        FragmentTransaction ft = getSupportFragmentManager().beginTransaction();
        ft.replace(R.id.main_content, frag, "transmissao-frag").commit();
        toggleFastMenu(FastMenu.MENU_NONE);
    }

    public void toAgenda()
    {
        frag = new AgendaFragment();
        FragmentTransaction ft = getSupportFragmentManager().beginTransaction();
        ft.replace(R.id.main_content, frag, "agenda-frag").commit();
        toggleFastMenu(FastMenu.MENU_AGENDA);
    }

    public void toSmart()
    {
        frag = new SmartFragment();
        FragmentTransaction ft = getSupportFragmentManager().beginTransaction();
        ft.replace(R.id.main_content, frag, "smart-frag").commit();
        toggleFastMenu(FastMenu.MENU_SMART);
    }

    public void toBookmark()
    {
        frag = new BookmarkFragment();
        FragmentTransaction ft = getSupportFragmentManager().beginTransaction();
        ft.replace(R.id.main_content, frag, "bookmark-frag").commit();
        toggleFastMenu(FastMenu.MENU_NONE);
    }

    public void toPedidosDeOracao()
    {
        frag = new OracaoFragment();
        FragmentTransaction ft = getSupportFragmentManager().beginTransaction();
        ft.replace(R.id.main_content, frag, "oracao-frag").commit();
        toggleFastMenu(FastMenu.MENU_NONE);
    }

    public void doLogout()
    {
        this.setUserToken("");
        Intent intent = new Intent(
                SmartChurch.this, Login.class
        );
        startActivity(intent);
        finish();
    }

    @Override
    public void onHomeFragInteraction() {

    }

    @Override
    public void onHomeToMembresia() {
        this.toMembresia();
    }

    @Override
    public void onHomeToPerfil() {
        this.toPerfil();
    }

    @Override
    public void onHomeToPalavra() {
        this.toPalavra(false);
    }

    @Override
    public void onHomeToTransmissao() {
        this.toTransmissao();
    }

    @Override
    public void onHomeToPalavraData(Object data) {
        this.transfer = data;
        this.toPalavra(true);
    }

    @Override
    public void onHomeToEstudoData(Object data) {
        this.transfer = data;
        this.toEstudo(true);
    }

    @Override
    public void onPerfilFragInteraction() {

    }

    @Override
    public void onPalavraFragmentFullyLoaded() {
        if(transfer != null) {
            ((PalavraFragment) frag).onSelectSermao(transfer);
        }
    }

    @Override
    public void onEstudoFragmentFullyLoaded() {
        if(transfer != null) {
            ((EstudoFragment) frag).onSelectEstudo(transfer);
        }
    }

    @Override
    public void onMembresiaFragmentInteraction() {

    }

    @Override
    public void onPalavraFragmentInteraction() {

    }

    @Override
    public void onEstudoFragmentInteraction() {

    }


    @Override
    public void onTransmissaoFragInteraction() {

    }

    @Override
    public void onAgendaFragInteraction() {

    }

    @Override
    public void onSmartFragInteraction() {

    }

    @Override
    public void onBookmarkFragInteraction() {

    }

    @Override
    public void onOracaoFragmentInteraction() {

    }

    /**
     * Classe estática com metadados sobre o menu rápido
     */
    public static class FastMenu
    {
        public static final int POS_INNER = 0;
        public static final int POS_LEFT = 1;
        public static final int POS_RIGHT = 2;

        public static final int MENU_NONE = -1;
        public static final int MENU_HOME = 10;
        public static final int MENU_PALAVRA = 11;
        public static final int MENU_PERFIL = 12;
        public static final int MENU_MEMBRO = 13;
        public static final int MENU_AGENDA = 14;
        public static final int MENU_SMART = 15;

        public static final FastMenuItem[] MENUS = {
                new FastMenuItem(MENU_HOME, POS_INNER, R.id.fastmenu_home, R.id.fastmenu_home_img, R.id.fastmenu_home_text, true),
                new FastMenuItem(MENU_PALAVRA, POS_RIGHT, R.id.fastmenu_palavra, R.id.fastmenu_palavra_img, R.id.fastmenu_palavra_text, true),
                new FastMenuItem(MENU_PERFIL, POS_RIGHT, R.id.fastmenu_perfil, R.id.fastmenu_perfil_img, R.id.fastmenu_perfil_text, false),
                new FastMenuItem(MENU_MEMBRO, POS_LEFT, R.id.fastmenu_membro, R.id.fastmenu_membro_img, R.id.fastmenu_membro_text, false),
                new FastMenuItem(MENU_AGENDA, POS_LEFT, R.id.fastmenu_agenda, R.id.fastmenu_agenda_img, R.id.fastmenu_agenda_text, true),
                new FastMenuItem(MENU_SMART, POS_INNER, R.id.fastmenu_smart, R.id.fastmenu_smart_img, R.id.fastmenu_smart_text, true)
        };
    }

    /**
     * Classe estática com metadados sobre itens do menu rápido
     */
    public static class FastMenuItem
    {
        public int MENU;
        public int POSITION;
        public int viewId;
        public int imgId;
        public int txtId;
        public boolean enabled;
        public boolean active;

        public FastMenuItem(int m, int p, int v, int i, int t, boolean e)
        {
            this.MENU = m;
            this.POSITION = p;
            this.viewId = v;
            this.imgId = i;
            this.txtId = t;
            this.enabled = e;
            this.active = false;
        }
    }
}
