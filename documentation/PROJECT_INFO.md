# ResBack - Feedback System

## Description

The project is entitled Development of Web-based Feedback System for CCIS Students' Using Sentimental Analysis and Algorithm Concern Ranking. The proposed project aims to develop a system that helps students send feedback anonymously and automate the workplace of administration or faculty. In addition, this allows to encourage students to report campus issue and have easier access while also help the administration to identify frequent issues within the campus, allowing them to prioritze urgent concerns. 

## Process

* The student will access the system to give feedback to the campus.  
* The feedback will then undergo NLP preprocessing stage, which cleans the data and normalizing the text. 
* Then, the processed feedback will passed to the XLM-R tokenizer to convert text into tokens, allowing the model to process the data. 
* The model XLM - RoBERTa will classify the feedback using sentimental analysis to determine either positive, neutral, or negative. 
* The outcome of the sentiment analysis will passed to the frequency ranking algorithm, allowing to sort the sentiment data to the most frequent feedback in the campus.
* This list of data will be displayed on the admistration/faculty dashboard, helping them identify the frequent issue of the campus. 


## Roles

* Team Lead/Full-Stack -> Julian Shaun Viloria
* Lead Developer -> Ckhiel Joshua Queypo
* Documentation Lead -> Leah Joy Orenza
* UI/UX Developer -> Marvin Jess Quina
* QA Tester -> Adrian Ballesteros 

## Tech Stack 

* Framework: Laravel
* Model: XLM - RoBERTa

