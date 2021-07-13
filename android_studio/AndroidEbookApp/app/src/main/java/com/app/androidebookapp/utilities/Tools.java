package com.app.androidebookapp.utilities;

import android.app.Activity;
import android.content.Context;
import android.net.ConnectivityManager;
import android.net.NetworkInfo;
import android.util.DisplayMetrics;
import android.view.Display;

import com.google.ads.mediation.admob.AdMobAdapter;
import com.google.android.gms.ads.AdRequest;
import com.google.android.gms.ads.AdSize;

import java.io.ByteArrayOutputStream;
import java.io.InputStream;
import java.net.HttpURLConnection;
import java.net.URL;

import static com.app.androidebookapp.Config.USE_LEGACY_GDPR_EU_CONSENT;

public class Tools {
	
	Context context;

	// constructor
	public Tools(Context context) {
		this.context = context;
	}

	public static AdRequest getAdRequest(Activity activity) {
		if (USE_LEGACY_GDPR_EU_CONSENT) {
			return new AdRequest.Builder().addNetworkExtrasBundle(AdMobAdapter.class, GDPR.getBundleAd(activity)).build();
		} else {
			return new AdRequest.Builder().build();
		}
	}

	public static AdSize getAdSize(Activity activity) {
		// Step 2 - Determine the screen width (less decorations) to use for the ad width.
		Display display = activity.getWindowManager().getDefaultDisplay();
		DisplayMetrics outMetrics = new DisplayMetrics();
		display.getMetrics(outMetrics);
		float widthPixels = outMetrics.widthPixels;
		float density = outMetrics.density;
		int adWidth = (int) (widthPixels / density);
		// Step 3 - Get adaptive ad size and return for setting on the ad view.
		return AdSize.getCurrentOrientationAnchoredAdaptiveBannerAdSize(activity, adWidth);
	}

	public static String getJSONString(String url) {
		String jsonString = null;
		HttpURLConnection linkConnection = null;
		try {
			URL linkurl = new URL(url);
			linkConnection = (HttpURLConnection) linkurl.openConnection();
			int responseCode = linkConnection.getResponseCode();
			if (responseCode == HttpURLConnection.HTTP_OK) {
				InputStream linkinStream = linkConnection.getInputStream();
				ByteArrayOutputStream baos = new ByteArrayOutputStream();
				int j = 0;
				while ((j = linkinStream.read()) != -1) {
					baos.write(j);
				}
				byte[] data = baos.toByteArray();
				jsonString = new String(data);
			}
		} catch (Exception e) {
			e.printStackTrace();
		} finally {
			if (linkConnection != null) {
				linkConnection.disconnect();
			}
		}
		return jsonString;
	}
 	public static boolean isNetworkAvailable(Activity activity) {
		ConnectivityManager connectivity = (ConnectivityManager) activity
				.getSystemService(Context.CONNECTIVITY_SERVICE);
		if (connectivity == null) {
			return false;
		} else {
			NetworkInfo[] info = connectivity.getAllNetworkInfo();
			if (info != null) {
				for (int i = 0; i < info.length; i++) {
					if (info[i].getState() == NetworkInfo.State.CONNECTED) {
						return true;
					}
				}
			}
		}
		return false;
	}

 }
