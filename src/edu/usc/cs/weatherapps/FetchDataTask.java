package edu.usc.cs.weatherapps;

import java.util.HashMap;
import java.util.Map;

import org.json.JSONArray;
import org.json.JSONException;
import org.json.JSONObject;

import android.os.AsyncTask;

public class FetchDataTask extends AsyncTask<String, String, JSONObject> {


	@Override
	protected JSONObject doInBackground(String... args) {
		JSONParser jParser = new JSONParser();

		// Getting JSON from URL
		JSONObject json = jParser.getJSONFromUrl(args[0]);
		return json;
	}

	protected void onPostExecute(JSONObject json) {
		System.out.println("Inside onPostExecuteS");
		System.out.println(json);
		// MyDialog md = new MyDialog(MainActivity.getContext());
		try {

			// country
			String country = json.getJSONObject("weather")
					.getJSONObject("location").getString("country");
			//countryGlobal = country;
			System.out.println(country);

			// region
			String region = json.getJSONObject("weather")
					.getJSONObject("location").getString("region");
			System.out.println(region);

			// city
			String city = json.getJSONObject("weather")
					.getJSONObject("location").getString("city");
			System.out.println(city);

			// img
			String img = json.getJSONObject("weather").getString("img");
			System.out.println(img);

			// text
			String text = json.getJSONObject("weather")
					.getJSONObject("condition").getString("text");
			System.out.println(text);

			// temp
			String temp = json.getJSONObject("weather")
					.getJSONObject("condition").getString("temp");
			System.out.println(temp);

			// unit
			String unit = json.getJSONObject("weather").getJSONObject("units")
					.getString("temperature");
			System.out.println(unit);

			// Forecast array
			JSONArray jArr = json.getJSONObject("weather").getJSONArray(
					"forecast");

			System.out.println(jArr.length() + " length");
			Map<String, String> map = new HashMap<String, String>();

			for (int i = 0; i < jArr.length(); i++) {
				JSONObject jsonObject = jArr.getJSONObject(i);

				String day = jsonObject.getString("day");
				System.out.println(day);

				map.put("day", day);

				String weather = jsonObject.getString("text");
				System.out.println(weather);

				map.put("text", weather);

				String high = jsonObject.getString("high");
				System.out.println(high);

				map.put("high", high);

				String low = jsonObject.getString("low");
				System.out.println(low);

				map.put("low", low);
             
			}

		} catch (JSONException e) {
			// TODO Auto-generated catch block
			e.printStackTrace();
		}

	}

}
