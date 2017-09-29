# Easy Batch

#### biz.jmaconsulting.easybatch

## Overview

It's easier to create and export financial batches with Easy Batch:
- Create automatic daily batches for front end transactions
- Create automatic monthly batches for non-payment transactions
- Allow batch to be specified on backoffice forms when adding and editing contributions and payments
- Optionally require staff to specify batches on these backoffice forms

## Installation

1. If you have not already done so, setup Extensions Directory
    1. Go to Administer >> System Settings >> Directories
        1. Set an appropriate value for CiviCRM Extensions Directory. For example, for Drupal, [civicrm.files]/ext/
        1. In a different window, ensure the directory exists and is readable by your web server process.
    1. Click Save.
1. If you have not already done so, setup Extensions Resources URL
  1. Go to Administer >> System Settings >> Resource URLs
    1. Beside Extension Resource URL, enter an appropriate values such as [civicrm.files]/ext/
  1. Click Save.
1. Install Easy Batch extension
  1. Go to Administer >> Customize Data and Screens >> Manage Extensions.
  1. Click on Add New tabEasy Batch Emails in the list of extensions, download it and unzip it into the extensions direction setup above, then return to this page.
  1. Beside Easy Batch, click Download.
  1. Review the information, then click Download and Install.

## Configuration

### Configure Automatic Daily Batches for Front End Transactions

If desired, you can automatically create batch each day for the payment transactions for each payment processor as follows:

1. Navigate to Administer >> System Settings >> Payment Processors, then beside each processor click Edit.
1. Enable Create Automatic Daily Financial Batches?
1. If desired, adjust the Automatic Daily Batch Close Time. For example, set the time when the payment processor automatically closes its batches for the day so that the CiviCRM batches and payment processor batches both close at the same time and thus will have the same contents every day.

### Configure Automatic Monthly Batches for Non-payment Transactions

If desired, you can automatically create a batch each month and populate it with all non-payment transactions during the month as follows:

1. Navigate to Administer >> CiviContribute Component Settings.
1. For Automatically batch non-payment transactions?, select the file format you wish to create each month, .iif or .csv.

The batch will be created at the start of a new month containing the previous month's transactions, and will be available by navigating to Contributions >> Financial Batches >> Exported Batches. 

### Configure Options for Back End Forms

1. Navigate to Administer >> CiviContribute Component Settings
1. Enable Require Financial Batch on payments through Backoffice forms?

JMA Consulting develops and maintains this extension. 

Many thanks to IIDA for sponsoring the development of this extension, and also to LCD Services for collaborating during its development.
