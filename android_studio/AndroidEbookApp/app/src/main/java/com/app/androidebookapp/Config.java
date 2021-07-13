package com.app.androidebookapp;

public class Config {

    //this is the path of your admin panel url
    public static final String ADMIN_PANEL_URL = "http://65.2.143.214/android_ebook_app";

    //AdMob configuration
    public static final boolean ENABLE_ADMOB_BANNER_ADS = true;
    public static final boolean ENABLE_ADMOB_INTERSTITIAL_ADS = true;
    public static final int ADMOB_INTERSTITIAL_ADS_INTERVAL = 3;

    //ebook grid span count
    public static final int SPAN_COUNT = 3;

    //splash menu in milliseconds
    public static final int SPLASH_TIME = 3000;

    //set true if you want to enable RTL (Right To Left) mode, e.g : Arabic Language
    public static final boolean ENABLE_RTL_MODE = false;

    //GDPR EU Consent
    public static final boolean USE_LEGACY_GDPR_EU_CONSENT = true;

    //Built in pdf reader
    public static final boolean USE_BUILT_IN_PDF_READER = true;

    //display info in navigation drawer header
    public static final boolean ENABLE_DRAWER_INFO = true;

    //load more ebook pagination
    public static final int LOAD_MORE = 48;

}