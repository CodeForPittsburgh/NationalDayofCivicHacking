# National Day of Civic Hacking
June 6th 2015 <br>
# Vacant Properties Challenge
Read more about this challenge [here](http://iheartpgh.com/2015/06/06/digging-data-vacant-land-north-side-hackforchange/) and [here](http://technical.ly/2015/06/09/6-awesome-projects-years-national-day-civic-hacking/).

## Datasets
For this challenge we were given two datasets: (1) a geocoded classification of North Side vacant properties from a [survey](https://gtechstrategies.org/resources/strengthening-northside-communities/) conducted by [GTECH Strategies](https://gtechstrategies.org/what-we-do/reclaim/) and (2) Allegheny County property assessment data for all North Side neighborhoods. Our first task was to merge these datasets so that we could compare the GTECH vacant property classification to information in the county property assessment database. We then split the combined dataset into subsets in order to investigate discrepancies between the GTECH survey findings and county data. 

The resulting datasets are as follows: 
* `all_properties_combined.csv` — The full combined dataset including county assessment data for all North Side properties with GTECH survey data appended (23765 properties). 
* `only_GTECH.csv` — Includes only properties classified as vacant in the GTECH survey data, with county assessment data appended (6046 properties).
* `all_vacant_combined.csv` — Includes all properties classified as vacant in the GTECH survey data, with the addition of properties in the county assessment database with a building value of zero (8794 properties). 
* `county_discrepancies.csv` — Includes properties classified as vacant in the GTECH survey data where county records show a building value greater than zero (622 properties). 
* `GTECH_missing.csv` — Includes properties NOT included in the GTECH survey data where county records show a building value of zero (2748 properties). 


## Analysis and Visualization
To investigate what could be learned from the combined dataset, we selected two variables from the county records for further analysis: (1) `FairMarketLand`: the assessed fair market land value for North Side vacant properties and (2) `UseDesc`: the county's property use classification for those properties. We summarized these variables for each category in the GTECH classification scheme, and the results are shown below. 

![alt-text](https://github.com/danielnarey/NationalDayofCivicHacking/raw/master/Challenges/Vacant%20Properties/graphics/classification_by_use.svg)

## GIS Mapping
A few team members were also working on maps. *Please update this section with information about maps created by the team and share links or source files.*




