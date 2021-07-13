package com.app.androidebookapp.activities;

import android.annotation.SuppressLint;
import android.content.Intent;
import android.os.AsyncTask;
import android.os.Build;
import android.os.Bundle;
import android.os.Handler;
import androidx.swiperefreshlayout.widget.SwipeRefreshLayout;
import androidx.appcompat.app.ActionBar;
import androidx.appcompat.app.AppCompatActivity;
import androidx.recyclerview.widget.GridLayoutManager;
import androidx.recyclerview.widget.LinearLayoutManager;
import androidx.recyclerview.widget.RecyclerView;
import androidx.appcompat.widget.Toolbar;
import android.view.MenuItem;
import android.view.View;
import android.widget.FrameLayout;
import android.widget.Toast;

import com.app.androidebookapp.Config;
import com.app.androidebookapp.R;
import com.app.androidebookapp.adapters.AdapterStory;
import com.app.androidebookapp.models.ItemStory;
import com.app.androidebookapp.utilities.Constant;
import com.app.androidebookapp.utilities.EndlessRecyclerViewScrollListener;
import com.app.androidebookapp.utilities.GDPR;
import com.app.androidebookapp.utilities.Tools;
import com.google.ads.mediation.admob.AdMobAdapter;
import com.google.android.gms.ads.AdListener;
import com.google.android.gms.ads.AdRequest;
import com.google.android.gms.ads.AdView;
import com.google.android.gms.ads.InterstitialAd;
import com.google.android.gms.ads.LoadAdError;

import org.json.JSONArray;
import org.json.JSONException;
import org.json.JSONObject;

import java.util.ArrayList;

public class ActivityStory extends AppCompatActivity {

    GridLayoutManager gridLayoutManager;
    RecyclerView recyclerView;
    AdapterStory adapterStory;
    ArrayList<ItemStory> itemStories;
    Boolean isOver = false;
    SwipeRefreshLayout swipeRefreshLayout = null;
    private EndlessRecyclerViewScrollListener scrollListener;
    private AdView adView;
    private InterstitialAd interstitialAd;
    int counter = 1;

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_story_list);

        if (Config.ENABLE_RTL_MODE) {
            if (Build.VERSION.SDK_INT >= Build.VERSION_CODES.JELLY_BEAN_MR1) {
                getWindow().getDecorView().setLayoutDirection(View.LAYOUT_DIRECTION_RTL);
            }
        }

        swipeRefreshLayout = (SwipeRefreshLayout) findViewById(R.id.swipeRefreshLayout);
        swipeRefreshLayout.setColorSchemeResources(R.color.orange, R.color.green, R.color.blue, R.color.red);

        final Toolbar toolbar = (Toolbar) findViewById(R.id.toolbar);
        setSupportActionBar(toolbar);
        final ActionBar actionBar = getSupportActionBar();
        if (actionBar != null) {
            getSupportActionBar().setDisplayHomeAsUpEnabled(true);
            getSupportActionBar().setTitle(Constant.CATEGORY_TITLE);
        }

        loadBannerAd();
        loadInterstitialAd();

        itemStories = new ArrayList<ItemStory>();

        gridLayoutManager = new GridLayoutManager(this, 1);
        gridLayoutManager.setSpanSizeLookup(new GridLayoutManager.SpanSizeLookup() {
            @Override
            public int getSpanSize(int position) {
                return adapterStory.isHeader(position) ? gridLayoutManager.getSpanCount() : 1;
            }
        });

        recyclerView = (RecyclerView) findViewById(R.id.recyclerView);
        recyclerView.setHasFixedSize(true);
        recyclerView.setLayoutManager(gridLayoutManager);

        scrollListener = new EndlessRecyclerViewScrollListener(gridLayoutManager) {
            @Override
            public void onLoadMore(int p, int totalItemsCount) {
                itemStories.add(null);
                adapterStory.notifyItemInserted(itemStories.size() - 1);
                itemStories.remove(itemStories.size() - 1);
                adapterStory.notifyItemRemoved(itemStories.size());
                if (!isOver) {
                    new Handler().postDelayed(new Runnable() {
                        @Override
                        public void run() {
                            new MyTask().execute(Config.ADMIN_PANEL_URL + "/api/api.php?book_id=" + Constant.CATEGORY_ID);
                        }
                    }, 2000);
                } else {
                    adapterStory.hideHeader();
                }
            }
        };

        recyclerView.addOnScrollListener(scrollListener);

        if (Tools.isNetworkAvailable(ActivityStory.this)) {
            new MyTask().execute(Config.ADMIN_PANEL_URL + "/api/api.php?book_id=" + Constant.CATEGORY_ID);
        } else {
            Toast.makeText(getApplicationContext(), "Please enable your network", Toast.LENGTH_SHORT).show();
        }

        swipeRefreshLayout.setOnRefreshListener(new SwipeRefreshLayout.OnRefreshListener() {
            @Override
            public void onRefresh() {
                resetAdapter();
                new Handler().postDelayed(new Runnable() {
                    @Override
                    public void run() {
                        refreshContent();
                    }
                }, Constant.DELAY_LOAD_MORE);
            }
        });

    }

    public void resetAdapter() {
        recyclerView.getRecycledViewPool().clear();
        itemStories.clear();
        adapterStory.notifyDataSetChanged();
    }

    public void refreshContent() {
        if (Tools.isNetworkAvailable(ActivityStory.this)) {
            new MyTask().execute(Config.ADMIN_PANEL_URL + "/api/api.php?book_id=" + Constant.CATEGORY_ID);
        } else {
            Toast.makeText(getApplicationContext(), "please enable your network to refresh content", Toast.LENGTH_SHORT).show();
        }
        swipeRefreshLayout.setRefreshing(false);
    }

    @SuppressLint("StaticFieldLeak")
    private class MyTask extends AsyncTask<String, Void, String> {

        @Override
        protected void onPreExecute() {
            super.onPreExecute();
            if (itemStories.size() == 0) {
                swipeRefreshLayout.setRefreshing(true);
            }
        }

        @Override
        protected String doInBackground(String... params) {
            return Tools.getJSONString(params[0]);
        }

        @Override
        protected void onPostExecute(String result) {
            super.onPostExecute(result);

            if (itemStories.size() == 0) {
                swipeRefreshLayout.setRefreshing(false);
            }

            if (null == result || result.length() == 0) {
                adapterStory.hideHeader();
            } else {

                try {
                    JSONObject mainJson = new JSONObject(result);
                    JSONArray jsonArray = mainJson.getJSONArray(Constant.JSON_ARRAY_NAME);

                    if (jsonArray.length() <= itemStories.size() + Config.LOAD_MORE) {
                        isOver = true;
                    }

                    int a = itemStories.size();
                    int b = Config.LOAD_MORE;

                    JSONObject objJson = null;
                    for (int i = a; i < a + b; i++) {
                        objJson = jsonArray.getJSONObject(i);

                        String story_id = objJson.getString(Constant.STORY_ID);
                        String story_title = objJson.getString(Constant.STORY_TITLE);
                        String story_subtitle = objJson.getString(Constant.STORY_SUBTITLE);
                        String story_description = objJson.getString(Constant.STORY_DESCRIPTION);
                        String book_id = objJson.getString(Constant.BOOK_ID);

                        ItemStory objItem = new ItemStory(story_id, story_title, story_subtitle, story_description, book_id);
                        itemStories.add(objItem);
                    }

                } catch (JSONException e) {
                    e.printStackTrace();
                    isOver = true;
                }
                setAdapterToRecyclerView();
                onItemClickListener();
            }
        }
    }

    public void setAdapterToRecyclerView() {
        if (itemStories.size() < Config.LOAD_MORE + 1) {
            adapterStory = new AdapterStory(this, itemStories);
            recyclerView.setAdapter(adapterStory);
        } else {
            adapterStory.notifyDataSetChanged();
        }
        setEmptyText();
    }

    @Override
    public boolean onOptionsItemSelected(MenuItem menuItem) {
        switch (menuItem.getItemId()) {
            case android.R.id.home:
                onBackPressed();
                break;

            default:
                return super.onOptionsItemSelected(menuItem);
        }
        return true;
    }

    private void setEmptyText() {
        if (adapterStory.getItemCount() == 0) {
            //lyt_no_item.setVisibility(View.VISIBLE);
        } else {
            //lyt_no_item.setVisibility(View.INVISIBLE);
        }
    }

    @Override
    public void onResume() {
        if (adapterStory != null && adapterStory.getItemCount() > 0) {
            adapterStory.notifyDataSetChanged();
            setEmptyText();
        }
        super.onResume();
    }

    public void onItemClickListener() {
        adapterStory.setOnItemClickListener(new AdapterStory.OnItemClickListener() {
            @Override
            public void onItemClick(View v, ItemStory obj, int position) {
                Constant.arrayList.clear();
                Constant.arrayList.addAll(itemStories);
                Intent intent = new Intent(getApplicationContext(), ActivityStoryDetail.class);
                intent.putExtra("POSITION_ID", position);
                startActivity(intent);

                showInterstitialAd();
            }
        });
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

    private void loadInterstitialAd() {
        if (Config.ENABLE_ADMOB_INTERSTITIAL_ADS) {
            interstitialAd = new InterstitialAd(ActivityStory.this);
            interstitialAd.setAdUnitId(getResources().getString(R.string.admob_interstitial_unit_id));
            interstitialAd.loadAd(Tools.getAdRequest(ActivityStory.this));

            interstitialAd.setAdListener(new AdListener() {
                @Override
                public void onAdClosed() {
                    interstitialAd.loadAd(Tools.getAdRequest(ActivityStory.this));
                }
            });
        }
    }

    private void showInterstitialAd() {
        if (Config.ENABLE_ADMOB_INTERSTITIAL_ADS) {
            if (interstitialAd != null && interstitialAd.isLoaded()) {
                if (counter == Config.ADMOB_INTERSTITIAL_ADS_INTERVAL) {
                    interstitialAd.show();
                    counter = 1;
                } else {
                    counter++;
                }
            }
        }
    }

}