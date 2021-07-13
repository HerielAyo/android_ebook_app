package com.app.androidebookapp.activities;

import android.annotation.SuppressLint;
import android.app.ProgressDialog;
import android.content.Intent;
import android.net.Uri;
import android.os.AsyncTask;
import android.os.Bundle;
import android.util.Log;
import android.view.MenuItem;
import android.view.View;
import android.widget.FrameLayout;

import androidx.appcompat.app.ActionBar;
import androidx.appcompat.app.AppCompatActivity;
import androidx.appcompat.widget.Toolbar;

import com.app.androidebookapp.Config;
import com.app.androidebookapp.R;
import com.app.androidebookapp.utilities.GDPR;
import com.app.androidebookapp.utilities.Tools;
import com.github.barteksc.pdfviewer.PDFView;
import com.github.barteksc.pdfviewer.link.DefaultLinkHandler;
import com.github.barteksc.pdfviewer.listener.OnLoadCompleteListener;
import com.github.barteksc.pdfviewer.listener.OnPageChangeListener;
import com.github.barteksc.pdfviewer.listener.OnPageErrorListener;
import com.github.barteksc.pdfviewer.scroll.DefaultScrollHandle;
import com.github.barteksc.pdfviewer.util.FitPolicy;
import com.google.ads.mediation.admob.AdMobAdapter;
import com.google.android.gms.ads.AdListener;
import com.google.android.gms.ads.AdRequest;
import com.google.android.gms.ads.AdView;
import com.google.android.gms.ads.LoadAdError;
import com.google.android.gms.ads.MobileAds;
import com.shockwave.pdfium.PdfDocument;

import java.io.BufferedInputStream;
import java.io.IOException;
import java.io.InputStream;
import java.net.HttpURLConnection;
import java.net.URL;
import java.util.List;

public class ActivityPdfReader extends AppCompatActivity implements OnPageChangeListener, OnLoadCompleteListener, OnPageErrorListener {

    private static final String TAG = MainActivity.class.getSimpleName();
    private PDFView pdfView;
    Integer pageNumber = 0;
    String str_title, str_url;
    int position;
    Toolbar toolbar;
    ActionBar actionBar;
    private AdView adView;

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_pdf_reader);

        pdfView = findViewById(R.id.pdfView);

        Intent intent = getIntent();
        position = intent.getIntExtra("POSITION", 0);
        str_title = intent.getStringExtra("TITLE");
        str_url = intent.getStringExtra("URL");

        loadURL loadurl = new loadURL(ActivityPdfReader.this);
        loadurl.execute(str_url);

        setupToolbar();
        loadBannerAd();

    }

    public void setupToolbar() {
        toolbar = findViewById(R.id.toolbar);
        setSupportActionBar(toolbar);
        actionBar = getSupportActionBar();
        if (actionBar != null) {
            getSupportActionBar().setDisplayHomeAsUpEnabled(true);
            getSupportActionBar().setTitle(str_title);
            //getSupportActionBar().setTitle(str_title);
        }
    }

    @Override
    public void onPageChanged(int page, int pageCount) {
        pageNumber = page;
        toolbar.setSubtitle(String.format("%s %s / %s", "", page + 1, pageCount));
        //setTitle(String.format("%s %s / %s", str_title, page + 1, pageCount));
//        if (actionBar != null) {
//            getSupportActionBar().setSubtitle(String.format("%s %s / %s", "", page + 1, pageCount));
//        }

    }

    @Override
    public void loadComplete(int nbPages) {
        PdfDocument.Meta meta = pdfView.getDocumentMeta();
        Log.e(TAG, "title = " + meta.getTitle());
        Log.e(TAG, "author = " + meta.getAuthor());
        Log.e(TAG, "subject = " + meta.getSubject());
        Log.e(TAG, "keywords = " + meta.getKeywords());
        Log.e(TAG, "creator = " + meta.getCreator());
        Log.e(TAG, "producer = " + meta.getProducer());
        Log.e(TAG, "creationDate = " + meta.getCreationDate());
        Log.e(TAG, "modDate = " + meta.getModDate());

        printBookmarksTree(pdfView.getTableOfContents(), "-");

    }

    public void printBookmarksTree(List<PdfDocument.Bookmark> tree, String sep) {
        for (PdfDocument.Bookmark b : tree) {
            Log.e(TAG, String.format("%s %s, p %d", sep, b.getTitle(), b.getPageIdx()));
            if (b.hasChildren()) {
                printBookmarksTree(b.getChildren(), sep + "-");
            }
        }
    }

    @Override
    public void onPageError(int page, Throwable t) {
        Log.e(TAG, "Cannot load page " + page);
    }

    @SuppressLint("StaticFieldLeak")
    public class loadURL extends AsyncTask<String, Void, InputStream> {

        private final ProgressDialog progressDialog;

        loadURL(ActivityPdfReader loadPdf) {
            progressDialog = new ProgressDialog(loadPdf);
        }

        @Override
        protected void onPreExecute() {
            super.onPreExecute();

            progressDialog.setTitle("Please wait");
            progressDialog.setMessage("Fetching PDF from server...");
            progressDialog.setCancelable(false);
            progressDialog.setCanceledOnTouchOutside(false);
            progressDialog.show();

        }

        @Override
        protected InputStream doInBackground(String... strings) {
            InputStream inputStream = null;
            try {
                URL url = new URL(strings[0]);
                HttpURLConnection urlConnection = (HttpURLConnection) url.openConnection();
                if (urlConnection.getResponseCode() == 200) {
                    inputStream = new BufferedInputStream(urlConnection.getInputStream());
                }
            } catch (IOException e) {
                return null;
            }
            return inputStream;
        }

        @Override
        protected void onPostExecute(InputStream inputStream) {
            //pdfFileName = getFileName(mypdf);
            pdfView.fromStream(inputStream)
                    .linkHandler(new DefaultLinkHandler(pdfView))
                    .defaultPage(pageNumber)
                    .defaultPage(pageNumber)
                    .onPageChange(ActivityPdfReader.this)
                    .enableAnnotationRendering(true)
                    .onLoad(ActivityPdfReader.this)
                    .scrollHandle(new DefaultScrollHandle(ActivityPdfReader.this))
                    .spacing(0) // in dp
                    .onPageError(ActivityPdfReader.this)

                    .swipeHorizontal(true)
                    .pageSnap(true)
                    .autoSpacing(true)
                    .pageFling(true)

                    .pageFitPolicy(FitPolicy.WIDTH)
                    //.nightMode(true)

                    .load();
            progressDialog.dismiss();
        }
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