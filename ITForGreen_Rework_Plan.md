
# 💡 ITForGreen – Smart Rework Plan

## 🧱 GOAL:
Refactor the project to optimize:
- Data handling (buildings vs apartments)
- API usage (ADEME)
- Frontend sync with backend
- Eliminate broken logic and simplify models

---

## 🔁 System Overview

### 🟢 Runtime Behavior
1. On user entry → we get the user's central coordinates.
2. These coordinates are converted to a **tile ID** (ex: 1km² grid).
3. If the tile hasn’t been scanned yet:
   - We query the ADEME API with bounding box coordinates
   - We save unique apartments into the DB (based on `dpe_code`)
   - We group them under a `building` (with aggregated data like average DPE)
4. On the frontend:
   - Only `buildings` are displayed (one icon per structure)
   - Clicking reveals aggregated or detailed apartment data if needed
5. If the user moves → repeat from step 2 for the new area

---

## 🗂️ Database Structure

### `apartments`
| Column              | Type            | Notes                        |
|---------------------|-----------------|------------------------------|
| `id`                | UUID            | Primary key                  |
| `dpe_code`          | string (unique) | From ADEME                   |
| `latitude`          | decimal         | From ADEME                   |
| `longitude`         | decimal         | From ADEME                   |
| `dpe_grade`         | string          | A-G                          |
| `energy_consumption`| float           | kWh/m²/year                  |
| `building_id`       | FK              | Link to `buildings` table    |

### `buildings`
| Column        | Type    | Notes                         |
|---------------|---------|-------------------------------|
| `id`          | UUID    | Primary key                   |
| `latitude`    | decimal | Center point (for map)        |
| `longitude`   | decimal |                               |
| `dpe_grade`   | string  | Aggregated (calculated)       |
| `energy_avg`  | float   | Avg from related apartments   |
| `carbon_avg`  | float   | Optional                      |

---

## ⚙️ API Interaction Plan

- ADEME API is only called **when a tile has never been scanned**
- Tile = 1km² geographical bounding box (`lat/lng` rounded to `0.01`)
- Apartments are only inserted **if `dpe_code` is not in DB**
- Use Laravel `Job` to handle the API call + insert logic

---

## 💾 Data Storage Strategy

- Only store apartments **for scanned areas**
- Buildings are created from grouped apartments
- Add Artisan job to **clean up apartments older than 1-3 months**
- Avoid ever storing “whole France”

---

## 📌 Blackbox (BB) Tasks

1. ✅ Implement the **tile calculation function** (based on lat/lng)
2. ✅ Build the `apartments` and `buildings` models + migrations
3. ✅ Write the `FetchAdemeDataJob`
   - Accepts center lat/lng
   - Generates bounding box
   - Calls API
   - Inserts new apartments
   - Groups by building (with rounding)
4. 🛠️ (Later) Add controller or service to trigger the job on frontend map movement
5. 🧹 (Optional) Artisan command to delete outdated apartments


### Data inspired from JSON_response : 
## 🧱 Model: `apartments`

| Column              | Type             | Description                                  |
|---------------------|------------------|----------------------------------------------|
| `id`                | UUID             | Primary key                                  |
| `dpe_code`          | string (unique)  | ADEME identifier (`numero_dpe`)              |
| `latitude`          | decimal(10, 6)   | Latitude coordinate                          |
| `longitude`         | decimal(10, 6)   | Longitude coordinate                         |
| `surface_area`      | float            | Thermal surface (`surface_thermique_lot`)    |
| `year_built`        | integer          | Year built (`annee_construction`)            |
| `dpe_grade`         | string(1)        | Energy grade A-G (`classe_consommation_energie`) |
| `ges_grade`         | string(1)        | GES grade A-G (`classe_estimation_ges`)      |
| `energy_consumption`| float            | Energy consumption (kWh/m²/year)             |
| `carbon_emission`   | float            | CO₂ emission (kgCO₂/m²/year)                 |
| `raw_ademe_data`    | json             | Full raw API response for this entry         |
| `building_id`       | foreignId        | Link to `buildings.id`                       |
| `created_at` / `updated_at` | timestamps | Laravel standard timestamps                  |

---

## 🏢 Model: `buildings`

| Column                  | Type             | Description                                |
|-------------------------|------------------|--------------------------------------------|
| `id`                    | UUID             | Primary key                                |
| `latitude`              | decimal(10, 6)   | Central or average position                |
| `longitude`             | decimal(10, 6)   | Central or average position                |
| `address_text`          | string (nullable)| Address text if available (`geo_adresse`)  |
| `avg_dpe_grade`         | string(1)        | Calculated average DPE grade               |
| `avg_ges_grade`         | string(1)        | Calculated average GES grade               |
| `avg_energy_consumption`| float            | Mean of all related apartments             |
| `avg_carbon_emission`   | float            | Mean of all related apartments             |
| `apartments_count`      | integer          | Number of apartments linked                |
| `created_at` / `updated_at` | timestamps   | Laravel standard timestamps                |

---