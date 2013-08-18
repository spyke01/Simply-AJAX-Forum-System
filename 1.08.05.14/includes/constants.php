<?php 
/***************************************************************************
 *                               constants.php
 *                            -------------------
 *   begin                : Tuseday, March 14, 2006
 *   copyright            : (C) 2006 Fast Track Sites
 *   email                : sales@fasttracksites.com
 *
 *
 ***************************************************************************/

/***************************************************************************
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU Lesser General Public
 * License as published by the Free Software Foundation; either
 * version 2.1 of the License, or (at your option) any later version.
 * 
 * This program is also licensed under a program license located inside 
 * the license.txt file included with this program. This license overrides
 * all license statements made in the previous license reveference. 
 ***************************************************************************/

//=====================================================
// Debug Level
//=====================================================
//define('DEBUG', 1); // Debugging on
define('DEBUG', 0); // Debugging off

//=====================================================
// Global state
//=====================================================
define('ACTIVE', 1);
define('INACTIVE', 0);

//=====================================================
// User Levels
//=====================================================
define('USER', 0);
define('ADMIN', 1);
define('MOD', 2);
define('BANNED', 3);


//=====================================================
// User related
//=====================================================
define('USER_AVATAR_NONE', 0);
define('USER_AVATAR_UPLOAD', 1);
define('USER_AVATAR_REMOTE', 2);
define('USER_AVATAR_GALLERY', 3);


//=====================================================
// Forum state
//=====================================================
define('FORUM_UNLOCKED', 0);
define('FORUM_LOCKED', 1);


//=====================================================
// Topic status
//=====================================================
define('TOPIC_UNLOCKED', 0);
define('TOPIC_LOCKED', 1);
define('TOPIC_MOVED', 2);
define('TOPIC_WATCH_NOTIFIED', 1);
define('TOPIC_WATCH_UN_NOTIFIED', 0);


//=====================================================
// Topic types
//=====================================================
define('POST_NORMAL', 0);
define('POST_STICKY', 1);
define('POST_ANNOUNCE', 2);
define('POST_GLOBAL_ANNOUNCE', 3);


//=====================================================
// Private messaging
//=====================================================
define('PRIVMSGS_READ_MAIL', 0);
define('PRIVMSGS_NEW_MAIL', 1);
define('PRIVMSGS_SENT_MAIL', 2);
define('PRIVMSGS_SAVED_IN_MAIL', 3);
define('PRIVMSGS_SAVED_OUT_MAIL', 4);
define('PRIVMSGS_UNREAD_MAIL', 5);

define('MSG_READ', 1);
define('MSG_NOTIFIED', 1);
define('INBOX', 0);
define('SAVEDFOLDER', 1);


//=====================================================
// Text values
//=====================================================
/* For index.php */
$T_Forum = "Forum";
$T_Topics = "Topics";
$T_Posts = "Posts";
$T_Last_Post = "Last Post";

/* For join.php */
$T_First_Name_Error = "First Name is a required field. Please try again.";
$T_Last_Name_Error = "Last Name is a required field. Please try again.";
$T_Email_Address_Error = "Please input a valid email address. This field is required.";
$T_Desired_Username_Error = "Desired Username is a required field. Please try again.";
$T_Password_Error = "Your passwords must be at least 6 characters and must not be the same as your username.";
$T_Email_Address_Taken = "Your email address has already been used by another member in our database. Please submit a different Email address!";
$T_Desired_Username_Taken = "The username you have selected has already been used by another member in our database. Please choose a different Username!";
$T_User_Info = "User Information";
$T_Mandatory_Info_Warning = "This info is mandatory, once submitted, you will be given a password. Please fill out all fields honestly.";
$T_Desired_Username = "Desired Username: ";
$T_Password = "Password: ";
$T_Confirm_Password = "Confirm Password: ";
$T_Email_Address = "Email Address: ";
$T_First_Name = "First Name: ";
$T_Last_Name = "Last Name: ";
$T_Gender = "Gender: ";
$T_Optional_Info = "Optional Information";
$T_Optional_Info_Warning = "This info is optional, and you may fill it out if you would like to.";
$T_Country = "Country: ";
$T_Intrests = "Intrests: ";

/* For login.php */
$T_Login = "Login";
$T_Username = "Username: ";
// $T_Password = ""; // Already set in the join.php section

/* For memberlist.php */
$T_Members = "Current Members";

/* NC Items Are Items That Have Been Defined Before, But These Versions Do Not Have a Colon - NC*/
$T_Username_NC = "Username";
$T_Gender_NC = "Gender";
$T_Email_Address_NC = "Email Address";
$T_Country_NC = "Country";
$T_Birthday_NC = "Birthday";
$T_Intrests_NC = "Intrests";
$T_Website_NC = "Website";
$T_AIM_NC = "AIM";
$T_YIM_NC = "YIM";
$T_MSN_NC = "MSN";
$T_ICQ_NC = "ICQ";
$T_Signature_NC = "Signature";
$T_Contact_Info_NC = "Contact Information";

/* For privimsgs.php */
$T_New_PM = "New Private Message";
$T_PM_To = "To(username): ";
$T_Subject = "Subject: ";
$T_Sent = "Sent";
$T_PM_Hacking_Attempt = "Please don't try to read other user's messages.";
$T_Messages = "Current Private Messages";
$T_Sender = "Sender";
$T_Date = "Date";
$T_No_Msgs = "No Messages in Current Folder";
$T_Menu = "Menu";
$T_Compose = "Compose";
$T_Inbox = "Inbox";
$T_Saved_Msgs = "Saved Msgs";

/* For profile.php */
$T_Viewing_Profile = "Viewing Profile: ";
$T_Active_Stats = "Activity Stats";
$T_Total_Posts = "Total Posts";
$T_Last_Active = "Last Active";
$T_Communication = "Communication Options";
$T_Profile_Information = "Information";
$T_Additional_Information = "Additional Information";
$T_Username = "Username: ";
$T_Title = "Title: ";
$T_Posts = "Posts: ";
$T_User_Level = "User Level: ";
$T_New_Password = "New Password";
$T_Confirm_New_Password = "Confirm New Password";
$T_Current_Avatar = "Your Current Avatar";
$T_Edit_Avatar = "Edit Your Avatar";
$T_Remote_Avatar = "Remote Avatar";
$T_Upload_Avatar = "Upload Avatar";
$T_Browse_Gallery = "Browse Gallery";
$T_Current_Signature = "Your Current Signature";
$T_Edit_Signature = "Edit Your Signature";

/* For search.php */ 
$T_Recent_Unread_Topics = "All Recent Unread Topics";
$T_Title = "Title";
$T_Poster = "Poster";
$T_Views = "Views";
$T_Replies = "Replies";
$T_Last_Post = "Last Post";
$T_Search_Results = "Search Results";
$T_Search = "Search";
$T_Keywords = "Keywords: ";

/* For viewforum.php */
$T_Sub_Forums = "Sub-Forums";
$T_New_Topic = "New Topic";
$T_Topic_Message = "Message";

/* For viewtopic.php */
$T_Posted = "Posted: ";
$T_Fast_Reply = "Fast Reply";
$T_Banned = "You cannot create or reply to any topics because your account has been suspended by an admin. You will be notified via email and/or private message as to why your account has been suspended as well as when it will be unsuspended.";

/* For admin pages */
$T_General_Configuration = "General Configuration";
$T_Board_Name = "Board Name";
$T_Disabled = "Disabled?";
$T_Board_Style = "Board Style: ";
$T_Board_Description = "Board Description: ";
$T_Max_Sig_Length = "Max Sig Length: ";
$T_Max_Avatar_Width = "Max Avatar Width: ";
$T_Max_Avatar_Height = "Max Avatar Height:";
$T_Max_Avatar_Filesize = "Max Avatar Filesize: ";
$T_Avatar_Path = "Avatar Path: ";
$T_Avatar_Gallery_Path = "Avatar Gallery Path: ";
$T_Board_Email = "Board Email: ";
$T_Table_Optimization_Utility = "Table Optimization Utility";
$T_Edit_Users = "Edit Users";

//=====================================================
// Application
//=====================================================
$A_Name = "fts_safs";
$A_Version = "1.08.05.14-PRO";
$A_License = "Pro Edition";
$A_Licensed_To = "Fast Track Sites";
?>