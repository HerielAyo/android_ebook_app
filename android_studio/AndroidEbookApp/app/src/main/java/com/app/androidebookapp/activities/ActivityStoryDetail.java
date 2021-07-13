package com.app.androidebookapp.activities;

import android.annotation.SuppressLint;
import android.content.Intent;
import android.content.res.Resources;
import android.graphics.Color;
import android.hardware.Sensor;
import android.hardware.SensorEvent;
import android.hardware.SensorEventListener;
import android.hardware.SensorManager;
import android.os.Build;
import android.os.Bundle;
import android.os.Handler;

import androidx.annotation.NonNull;
import androidx.viewpager.widget.PagerAdapter;
import androidx.viewpager.widget.ViewPager;
import androidx.viewpager.widget.ViewPager.OnPageChangeListener;
import androidx.appcompat.app.ActionBar;
import androidx.appcompat.app.AppCompatActivity;
import androidx.appcompat.widget.Toolbar;
import android.view.LayoutInflater;
import android.view.MenuItem;
import android.view.View;
import android.view.ViewGroup;
import android.webkit.WebSettings;
import android.webkit.WebView;
import android.webkit.WebViewClient;
import android.widget.FrameLayout;
import android.widget.TextView;

import com.app.androidebookapp.Config;
import com.app.androidebookapp.R;
import com.app.androidebookapp.utilities.Constant;
import com.app.androidebookapp.utilities.GDPR;
import com.app.androidebookapp.utilities.Tools;
import com.google.ads.mediation.admob.AdMobAdapter;
import com.google.android.gms.ads.AdListener;
import com.google.android.gms.ads.AdRequest;
import com.google.android.gms.ads.AdView;
import com.google.android.gms.ads.LoadAdError;
import com.google.android.gms.ads.MobileAds;

public class ActivityStoryDetail extends AppCompatActivity implements SensorEventListener {

    int position;
    ViewPager viewPager;
    int TOTAL_IMAGE;
    private SensorManager sensorManager;
    private boolean checkImage = false;
    private long lastUpdate;
    Handler handler;
    Runnable runnable;
    Toolbar toolbar;
    private AdView adView;

    @Override
    public void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_slider_story);

        if (Config.ENABLE_RTL_MODE) {
            if (Build.VERSION.SDK_INT >= Build.VERSION_CODES.JELLY_BEAN_MR1) {
                getWindow().getDecorView().setLayoutDirection(View.LAYOUT_DIRECTION_RTL);
            }
        }

        toolbar = (Toolbar) findViewById(R.id.toolbar);
        setSupportActionBar(toolbar);
        final ActionBar actionBar = getSupportActionBar();
        if (actionBar != null) {
            getSupportActionBar().setDisplayHomeAsUpEnabled(true);
            getSupportActionBar().setTitle(Constant.CATEGORY_TITLE);
        }

        loadBannerAd();

        Intent i = getIntent();
        position = i.getIntExtra("POSITION_ID", 0);

        TOTAL_IMAGE = Constant.arrayList.size() - 1;
        viewPager = (ViewPager) findViewById(R.id.image_slider);
        handler = new Handler();

        ImagePagerAdapter adapter = new ImagePagerAdapter();
        viewPager.setAdapter(adapter);
        viewPager.setCurrentItem(position);

        sensorManager = (SensorManager) getSystemService(SENSOR_SERVICE);
        lastUpdate = System.currentTimeMillis();

        viewPager.addOnPageChangeListener(new OnPageChangeListener() {

            @Override
            public void onPageSelected(int position) {

            }

            @Override
            public void onPageScrolled(int arg0, float arg1, int position) {

            }

            @Override
            public void onPageScrollStateChanged(int position) {

            }
        });

    }

    private class ImagePagerAdapter extends PagerAdapter {

        private LayoutInflater inflater;

        public ImagePagerAdapter() {
            inflater = getLayoutInflater();
        }

        @Override
        public int getCount() {
            return Constant.arrayList.size();

        }

        @Override
        public boolean isViewFromObject(View view, @NonNull Object object) {
            return view.equals(object);
        }

        @SuppressLint("SetJavaScriptEnabled")
        @NonNull
        @Override
        public Object instantiateItem(@NonNull ViewGroup container, final int position) {

            View imageLayout = inflater.inflate(R.layout.view_pager_item, container, false);
            assert imageLayout != null;

            final TextView txt_title = (TextView) imageLayout.findViewById(R.id.txt_title);
            final TextView txt_subtitle = (TextView) imageLayout.findViewById(R.id.txt_subtitle);
            final WebView webView_desc = (WebView) imageLayout.findViewById(R.id.txt_description);

            txt_title.setText(Constant.arrayList.get(position).getStory_title());
            txt_subtitle.setText(Constant.arrayList.get(position).getStory_subtitle());

            webView_desc.setBackgroundColor(Color.parseColor("#FFFFFF"));
            webView_desc.setFocusableInTouchMode(false);
            webView_desc.setFocusable(false);
            webView_desc.setOnLongClickListener(new View.OnLongClickListener() {
                @Override
                public boolean onLongClick(View v) {
                    return true;
                }
            });
            webView_desc.setLongClickable(false);

            webView_desc.setWebViewClient(new WebViewClient() {
                @Override
                public boolean shouldOverrideUrlLoading(WebView view, String url) {
                    return false;
                }
            });

            WebSettings webSettings = webView_desc.getSettings();
            Resources res = getResources();
            int fontSize = res.getInteger(R.integer.font_size);
            webSettings.setDefaultFontSize(fontSize);
            webSettings.setDefaultTextEncodingName("UTF-8");
            webSettings.setJavaScriptEnabled(true);

            String mimeType = "text/html; charset=UTF-8";
            String encoding = "utf-8";
            String htmlText = Constant.arrayList.get(position).getStory_description();

            if (Config.ENABLE_RTL_MODE) {
                String text = "<html dir='rtl'><head>"
                        + "<style type=\"text/css\">body{color: #525252;}"
                        + "</style></head>"
                        + "<body>"
                        + htmlText
                        + "</body></html>";
                webView_desc.loadDataWithBaseURL(null, text, mimeType, encoding, null);
            } else {
                String text = "<html><head>"
                        + "<style type=\"text/css\">body{color: #525252;}"
                        + "</style></head>"
                        + "<body>"
                        + htmlText
                        + "</body></html>";
                webView_desc.loadDataWithBaseURL(null, text, mimeType, encoding, null);
            }

            container.addView(imageLayout, 0);
            return imageLayout;

        }

        @Override
        public void destroyItem(@NonNull ViewGroup container, int position, @NonNull Object object) {
            ((ViewPager) container).removeView((View) object);
        }
    }

    @Override
    public void onAccuracyChanged(Sensor arg0, int arg1) {
    }


    @Override
    public void onSensorChanged(SensorEvent event) {
        if (event.sensor.getType() == Sensor.TYPE_ACCELEROMETER) {
            getAccelerometer(event);
        }
    }

    private void getAccelerometer(SensorEvent event) {
        float[] values = event.values;

        float x = values[0];
        float y = values[1];
        float z = values[2];

        float accelerationSquareRoot = (x * x + y * y + z * z)
                / (SensorManager.GRAVITY_EARTH * SensorManager.GRAVITY_EARTH);
        long actualTime = System.currentTimeMillis();
        if (accelerationSquareRoot >= 2) {
            if (actualTime - lastUpdate < 200) {
                return;
            }
            lastUpdate = actualTime;
            if (checkImage) {
                position = viewPager.getCurrentItem();
                viewPager.setCurrentItem(position);
            } else {
                position = viewPager.getCurrentItem();
                position++;
                if (position == TOTAL_IMAGE) {
                    position = TOTAL_IMAGE;
                }
                viewPager.setCurrentItem(position);
            }
            checkImage = !checkImage;
        }
    }


    @Override
    protected void onResume() {
        super.onResume();
        sensorManager.registerListener(this,
                sensorManager.getDefaultSensor(Sensor.TYPE_ACCELEROMETER),
                SensorManager.SENSOR_DELAY_NORMAL);
    }

    @Override
    protected void onPause() {
        super.onPause();
        sensorManager.unregisterListener(this);
    }

    @Override
    public void onDestroy() {
        super.onDestroy();
        handler.removeCallbacks(runnable);
        sensorManager.unregisterListener(this);
    }

    @Override
    public void onBackPressed() {
        super.onBackPressed();
    }

    @Override
    public boolean onOptionsItemSelected(MenuItem menuItem) {
        switch (menuItem.getItemId()) {
            case android.R.id.home:
                onBackPressed();
                return true;

            default:
                return super.onOptionsItemSelected(menuItem);
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

}
