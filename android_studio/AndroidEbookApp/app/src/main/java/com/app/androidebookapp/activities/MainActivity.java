package com.app.androidebookapp.activities;

import android.content.Intent;
import android.net.Uri;
import android.os.Build;
import android.os.Bundle;

import androidx.annotation.NonNull;

import com.app.androidebookapp.BuildConfig;
import com.app.androidebookapp.utilities.Tools;
import com.google.android.gms.ads.LoadAdError;
import com.google.android.material.navigation.NavigationView;

import androidx.core.view.GravityCompat;
import androidx.drawerlayout.widget.DrawerLayout;
import androidx.appcompat.app.ActionBarDrawerToggle;
import androidx.appcompat.app.AppCompatActivity;
import androidx.appcompat.widget.Toolbar;

import android.view.Menu;
import android.view.MenuInflater;
import android.view.MenuItem;
import android.view.View;
import android.widget.FrameLayout;
import android.widget.LinearLayout;
import android.widget.Toast;

import com.app.androidebookapp.Config;
import com.app.androidebookapp.R;
import com.app.androidebookapp.fragments.FragmentAbout;
import com.app.androidebookapp.fragments.FragmentBook;
import com.app.androidebookapp.fragments.FragmentFavorite;
import com.app.androidebookapp.utilities.GDPR;
import com.google.ads.mediation.admob.AdMobAdapter;
import com.google.android.gms.ads.AdListener;
import com.google.android.gms.ads.AdRequest;
import com.google.android.gms.ads.AdView;
import com.google.android.gms.ads.MobileAds;
import com.google.android.ump.ConsentDebugSettings;
import com.google.android.ump.ConsentForm;
import com.google.android.ump.ConsentInformation;
import com.google.android.ump.ConsentRequestParameters;
import com.google.android.ump.UserMessagingPlatform;

import static com.app.androidebookapp.Config.USE_LEGACY_GDPR_EU_CONSENT;

public class MainActivity extends AppCompatActivity implements NavigationView.OnNavigationItemSelectedListener {

    private final static String COLLAPSING_TOOLBAR_FRAGMENT_TAG = "collapsing_toolbar";
    private final static String SELECTED_TAG = "selected_index";
    private static int selectedIndex;
    private final static int COLLAPSING_TOOLBAR = 0;
    DrawerLayout drawerLayout;
    NavigationView navigationView;
    private AdView adView;
    private long exitTime = 0;
    private ActionBarDrawerToggle actionBarDrawerToggle;
    private ConsentInformation consentInformation;
    private ConsentForm consentForm;

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_main);

        if (Config.ENABLE_RTL_MODE) {
            if (Build.VERSION.SDK_INT >= Build.VERSION_CODES.JELLY_BEAN_MR1) {
                getWindow().getDecorView().setLayoutDirection(View.LAYOUT_DIRECTION_RTL);
            }
        }

        initAd();
        loadBannerAd();

        drawerLayout = findViewById(R.id.drawer_layout);
        navigationView = findViewById(R.id.navigation_view);
        navigationView.setNavigationItemSelectedListener(this);

        LinearLayout lyt_drawer_info = navigationView.getHeaderView(0).findViewById(R.id.lyt_drawer_info);
        if (!Config.ENABLE_DRAWER_INFO) {
            lyt_drawer_info.setVisibility(View.GONE);
        }

        if (savedInstanceState != null) {
            navigationView.getMenu().getItem(savedInstanceState.getInt(SELECTED_TAG)).setChecked(true);
            return;
        }

        selectedIndex = COLLAPSING_TOOLBAR;
        getSupportFragmentManager().beginTransaction()
                .add(R.id.fragment_container, new FragmentBook(), COLLAPSING_TOOLBAR_FRAGMENT_TAG)
                .commit();

    }

    @Override
    public boolean onNavigationItemSelected(@NonNull MenuItem menuItem) {

        switch (menuItem.getItemId()) {
            case R.id.menu_book:
                if (!menuItem.isChecked()) {
                    menuItem.setChecked(true);
                    getSupportFragmentManager().beginTransaction().replace(R.id.fragment_container, new FragmentBook(), COLLAPSING_TOOLBAR_FRAGMENT_TAG).commit();
                }
                drawerLayout.closeDrawer(GravityCompat.START);

                return true;

            case R.id.menu_read_later:

                if (!menuItem.isChecked()) {
                    menuItem.setChecked(true);
                    getSupportFragmentManager().beginTransaction().replace(R.id.fragment_container, new FragmentFavorite(), COLLAPSING_TOOLBAR_FRAGMENT_TAG).commit();
                }
                drawerLayout.closeDrawer(GravityCompat.START);

                return true;

            case R.id.drawer_rate:

                final String appName = getPackageName();
                try {
                    startActivity(new Intent(Intent.ACTION_VIEW, Uri.parse("market://details?id=" + appName)));
                } catch (android.content.ActivityNotFoundException anfe) {
                    startActivity(new Intent(Intent.ACTION_VIEW, Uri.parse("http://play.google.com/store/apps/details?id=" + appName)));
                }

                return true;

            case R.id.drawer_more:

                startActivity(new Intent(Intent.ACTION_VIEW, Uri.parse(getString(R.string.play_more_apps))));

                return true;

            case R.id.menu_about:
                if (!menuItem.isChecked()) {
                    menuItem.setChecked(true);
                    getSupportFragmentManager().beginTransaction().replace(R.id.fragment_container,
                            new FragmentAbout(), COLLAPSING_TOOLBAR_FRAGMENT_TAG).commit();
                }
                drawerLayout.closeDrawer(GravityCompat.START);

                return true;

        }
        return false;
    }

    public void setupNavigationDrawer(Toolbar toolbar) {
        actionBarDrawerToggle = new ActionBarDrawerToggle(this, drawerLayout, toolbar, R.string.drawer_open, R.string.drawer_close) {
            @Override
            public void onDrawerOpened(View drawerView) {
                super.onDrawerOpened(drawerView);
            }

            @Override
            public void onDrawerClosed(View drawerView) {
                super.onDrawerClosed(drawerView);
            }
        };
        drawerLayout.addDrawerListener(actionBarDrawerToggle);
        actionBarDrawerToggle.syncState();
    }

    @Override
    public boolean onCreateOptionsMenu(Menu menu) {
        MenuInflater inflater = getMenuInflater();
        inflater.inflate(R.menu.menu_search, menu);

        return true;
    }

    @Override
    public boolean onOptionsItemSelected(MenuItem menuItem) {
        switch (menuItem.getItemId()) {

            case android.R.id.home:
                onBackPressed();
                return true;

            case R.id.search:
                Intent intent = new Intent(getApplicationContext(), ActivitySearchBook.class);
                startActivity(intent);
                return true;

            default:
                return super.onOptionsItemSelected(menuItem);
        }
    }

    @Override
    public void onBackPressed() {
        DrawerLayout drawer = findViewById(R.id.drawer_layout);
        if (drawer.isDrawerOpen(GravityCompat.START)) {
            drawer.closeDrawer(GravityCompat.START);
        } else {
            exitApp();
        }
    }

    public void exitApp() {
        if ((System.currentTimeMillis() - exitTime) > 2000) {
            Toast.makeText(this, getString(R.string.press_again_to_exit), Toast.LENGTH_SHORT).show();
            exitTime = System.currentTimeMillis();
        } else {
            finish();
        }
    }

    public void initAd() {
        if (Config.ENABLE_ADMOB_BANNER_ADS || Config.ENABLE_ADMOB_INTERSTITIAL_ADS) {
            MobileAds.initialize(this, initializationStatus -> {
            });
            if (USE_LEGACY_GDPR_EU_CONSENT) {
                GDPR.updateConsentStatus(this);
            } else {
                updateConsentStatus();
            }
        }
    }

    public void loadBannerAd() {
        if (Config.ENABLE_ADMOB_BANNER_ADS) {
            FrameLayout adContainerView = findViewById(R.id.admob_banner_view_container);
            adContainerView.post(() -> {
                adView = new AdView(this);
                adView.setAdUnitId(getString(R.string.admob_banner_unit_id));
                adContainerView.removeAllViews();
                adContainerView.addView(adView);
                adView.setAdSize(Tools.getAdSize(this));
                adView.loadAd(Tools.getAdRequest(this));
                adView.setAdListener(new AdListener() {
                    @Override
                    public void onAdLoaded() {
                        // Code to be executed when an ad finishes loading.
                        adContainerView.setVisibility(View.VISIBLE);
                    }

                    @Override
                    public void onAdFailedToLoad(LoadAdError adError) {
                        // Code to be executed when an ad request fails.
                        adContainerView.setVisibility(View.GONE);
                    }

                    @Override
                    public void onAdOpened() {
                        // Code to be executed when an ad opens an overlay that
                        // covers the screen.
                    }

                    @Override
                    public void onAdClicked() {
                        // Code to be executed when the user clicks on an ad.
                    }

                    @Override
                    public void onAdLeftApplication() {
                        // Code to be executed when the user has left the app.
                    }

                    @Override
                    public void onAdClosed() {
                        // Code to be executed when the user is about to return
                        // to the app after tapping on an ad.
                    }
                });
            });
        }
    }

    public void updateConsentStatus() {
        if (BuildConfig.DEBUG) {
            ConsentDebugSettings debugSettings = new ConsentDebugSettings.Builder(this)
                    .setDebugGeography(ConsentDebugSettings.DebugGeography.DEBUG_GEOGRAPHY_NOT_EEA)
                    .addTestDeviceHashedId("TEST-DEVICE-HASHED-ID")
                    .build();
            ConsentRequestParameters params = new ConsentRequestParameters.Builder().setConsentDebugSettings(debugSettings).build();
            consentInformation = UserMessagingPlatform.getConsentInformation(this);
            consentInformation.requestConsentInfoUpdate(this, params, () -> {
                        if (consentInformation.isConsentFormAvailable()) {
                            loadForm();
                        }
                    },
                    formError -> {
                    });
        } else {
            ConsentRequestParameters params = new ConsentRequestParameters.Builder().build();
            consentInformation = UserMessagingPlatform.getConsentInformation(this);
            consentInformation.requestConsentInfoUpdate(this, params, () -> {
                        if (consentInformation.isConsentFormAvailable()) {
                            loadForm();
                        }
                    },
                    formError -> {
                    });
        }
    }

    public void loadForm() {
        UserMessagingPlatform.loadConsentForm(this, consentForm -> {
                    MainActivity.this.consentForm = consentForm;
                    if (consentInformation.getConsentStatus() == ConsentInformation.ConsentStatus.REQUIRED) {
                        consentForm.show(MainActivity.this, formError -> {
                            loadForm();
                        });
                    }
                },
                formError -> {
                }
        );
    }


}
