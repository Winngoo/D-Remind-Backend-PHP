<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Contracts\Routing\Registrar;
use App\Http\Controllers\Api\RegistrationController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\PaymentController;
use App\Http\Controllers\Api\MembershipController;
use App\Http\Controllers\Api\ReminderController;
use App\Http\Controllers\Api\CategoriesController;
use App\Http\Controllers\Api\NotificationController;
use App\Http\Controllers\Api\ProfileController;
use App\Http\Controllers\Api\Feedbackcontroller;
use App\Http\Controllers\Api\FaqController;
use App\Http\Controllers\Api\DashboardController;
use App\Http\Controllers\Api\UserManageController;
use App\Http\Controllers\Api\SubscriptionplanController;
use App\Http\Controllers\Api\CouponController;
use App\Http\Controllers\Api\HowItWorksController;
use App\Http\Controllers\Api\ContactUsController;
use App\Http\Controllers\Api\AboutUsController;
use App\Http\Controllers\Api\TermsAndConditionController;
use App\Http\Controllers\Api\PrivacyAndPolicyController;
use App\Http\Controllers\Api\DisclaimerController;
use App\Http\Controllers\Api\NewsController;
use App\Http\Controllers\Api\RolesController;
use App\Http\Controllers\Api\PermissionController;
use App\Http\Controllers\Api\SettingsController;
use App\Http\Controllers\Api\ReportsController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });


//User Authentication
Route::post('/register', [RegistrationController::class, 'UserRegister']);
Route::post('/login', [AuthController::class, 'UserLogin']);
Route::get('/login/{token}', [AuthController::class, 'UserLoginVerify']);
Route::post('/verify-mail', [AuthController::class, 'VerifyUserMail']);
Route::post('/reset-password', [AuthController::class, 'UserResetPassword']);
Route::middleware('auth.token')->post('/logout', [AuthController::class, 'UserLogout']);

//Payment
Route::get('/membership-payment-details', [PaymentController::class, 'Membershippaymentdetails']);
Route::post('/membership-payment', [PaymentController::class, 'Membershippayment']);

//Membership Management
Route::middleware('auth.token', 'check.permission:Membership View')->get('/membership-details/{id}', [MembershipController::class, 'UserMembershipDetails']);
Route::middleware('auth.token')->get('/membership-history/{id}', [MembershipController::class, 'UserMembershipHistory']);

//Reminder Management
Route::middleware('auth.token')->get('/reminder-view/{id}', [ReminderController::class, 'index']);
Route::middleware('auth.token', 'check.permission:Calendar Reminder Create')->post('/reminder-add/{id}', [ReminderController::class, 'store']);
Route::middleware('auth.token', 'check.permission:Calendar View')->get('/reminder-details/{id}', [ReminderController::class, 'show']);
Route::middleware('auth.token', 'check.permission:Calendar Reminder Edit')->put('/reminder-update/{id}', [ReminderController::class, 'update']);
Route::middleware('auth.token', 'check.permission:Calendar Reminder Delete')->delete('/reminder-delete/{id}', [ReminderController::class, 'destroy']);

//Categories Management
Route::middleware('auth.token')->get('/categories-list/{userId}', [CategoriesController::class, 'index']);
Route::post('/categories-add', [CategoriesController::class, 'store']);
Route::middleware('auth.token')->get('/categories-details/{id}', [CategoriesController::class, 'show']);
Route::put('/categories-update/{id}', [CategoriesController::class, 'update']);
Route::delete('/categories-delete/{id}', [CategoriesController::class, 'destroy']);

//Notification
Route::middleware('auth.token')->post('/notification-email-list/{userId}', [NotificationController::class, 'EmailNotificationList']);
Route::middleware('auth.token')->get('/reminder-notifications/{userId}', [NotificationController::class, 'ReminderNotificationsUser']);

//Profile Management
Route::middleware('auth.token', 'check.permission:Profile View')->get('/profile-details/{id}', [ProfileController::class, 'Profileshow']);
Route::middleware('auth.token', 'check.permission:Profile Details Update')->put('/profile-update/{id}', [ProfileController::class, 'Profileupdate']);
Route::middleware('auth.token', 'check.permission:Profile Password Update')->put('/profile-password-update/{id}', [ProfileController::class, 'Userpasswordupdate']);
Route::middleware('auth.token', 'check.permission:Profile Email Update')->put('/profile-email-update/{id}', [ProfileController::class, 'Useremailupdate']);
Route::middleware('auth.token', 'check.permission:Profile Picture Update')->post('/profile-picture-upload/{id}', [ProfileController::class, 'Userpictureupload']);

//Feedback Management
Route::middleware('auth.token')->get('/feedback-view/{userid}', [Feedbackcontroller::class, 'index']);
Route::middleware('auth.token', 'check.permission:Feedback Add')->post('/feedback-add/{userid}', [Feedbackcontroller::class, 'store']);
Route::middleware('auth.token', 'check.permission:Feedback View')->get('/feedback-details/{id}', [Feedbackcontroller::class, 'show']);
Route::middleware('auth.token', 'check.permission:Feedback Edit')->put('/feedback-update/{id}', [Feedbackcontroller::class, 'update']);
Route::middleware('auth.token', 'check.permission:Feedback Delete')->delete('/feedback-delete/{id}', [Feedbackcontroller::class, 'destroy']);

//FAQ
Route::get('/user-faq', [FaqController::class, 'FaqviewUsers']);

//Admin Authentication
Route::post('/admin-login', [AuthController::class, 'AdminLogin']);
Route::post('/admin-verify-mail', [AuthController::class, 'VerifyAdminMail']);
Route::post('/admin-reset-password/{token}', [AuthController::class, 'AdminResetPassword']);
Route::middleware('auth.admintoken')->post('/admin-logout', [AuthController::class, 'AdminLogout']);

//Admin Dashboard Management
Route::middleware('auth.admintoken')->get('/users-count', [DashboardController::class, 'ActiveUsersCount']);
Route::middleware('auth.admintoken')->get('/users-latest-register', [DashboardController::class, 'LatestRegistrationList']);
Route::middleware('auth.admintoken')->get('/reminders-count', [DashboardController::class, 'TotalRemindersCount']);
Route::middleware('auth.admintoken')->get('/popular-category-list', [DashboardController::class, 'PopularCategoryList']);
Route::middleware('auth.admintoken')->get('/subscription-details', [DashboardController::class, 'SubscriptionRevenueDetails']);
Route::middleware('auth.admintoken')->get('/reminder-banners-count', [DashboardController::class, 'ReminderBannersCount']);

//Admin User Management
Route::middleware('auth.admintoken')->get('/users-details-view', [UserManageController::class, 'UserDetailsView']);
Route::middleware('auth.admintoken')->get('/users-reminders-view/{userid}', [UserManageController::class, 'UserRemindersView']);
Route::middleware('auth.admintoken')->post('/users-details-edit/{userid}', [UserManageController::class, 'UserDetailsEdit']);
Route::middleware('auth.admintoken')->delete('/users-details-delete/{userid}', [UserManageController::class, 'UserDetailsDelete']);
Route::middleware('auth.admintoken')->post('/users-search', [UserManageController::class, 'UserSearch']);
Route::middleware('auth.admintoken')->post('/users-filter', [UserManageController::class, 'UserFilter']);
Route::middleware('auth.admintoken')->get('/users-roleslist', [UserManageController::class, 'UserRolesList']);

//Admin Calendar Management
Route::middleware('auth.admintoken')->get('/users-list-calendar', [UserManageController::class, 'UserListCalendar']);
Route::middleware('auth.admintoken')->get('/users-reminders-calendar/{userid}', [UserManageController::class, 'UserRemindersCalendar']);

//Admin Subscriptionplan Management
Route::middleware('auth.admintoken')->post('/subscription-plan/create', [SubscriptionplanController::class, 'CreateSubscriptionPlan']);
Route::middleware('auth.admintoken')->get('/subscription-plan/view', [SubscriptionplanController::class, 'ViewSubscriptionPlan']);
Route::middleware('auth.admintoken')->put('/subscription-plan/edit/{id}', [SubscriptionplanController::class, 'EditSubscriptionPlan']);
Route::middleware('auth.admintoken')->delete('/subscription-plan/delete/{id}', [SubscriptionplanController::class, 'DeleteSubscriptionPlan']);

//Admin Track Payments
Route::middleware('auth.admintoken')->get('/transaction-history', [PaymentController::class, 'TransactionHistory']);
Route::middleware('auth.admintoken')->post('/payment-search', [PaymentController::class, 'SearchPayment']);
Route::middleware('auth.admintoken')->post('/payment-filter', [PaymentController::class, 'SearchFilter']);

//Admin Coupon Management
Route::middleware('auth.admintoken')->post('/coupon/create', [CouponController::class, 'CreateCoupon']);
Route::middleware('auth.admintoken')->get('/coupon/view', [CouponController::class, 'CreateView']);
Route::middleware('auth.admintoken')->put('/coupon/edit/{id}', [CouponController::class, 'CreateEdit']);
Route::middleware('auth.admintoken')->delete('/coupon/delete/{id}', [CouponController::class, 'CreateDelete']);

//Admin Notification Control
Route::middleware('auth.admintoken')->post('/email-notification/{reminderid}', [NotificationController::class, 'EmailNotificationControl']);
Route::middleware('auth.admintoken')->post('/sms-notification/{reminderid}', [NotificationController::class, 'SMSNotificationControl']);

//Admin How It Works
Route::middleware('auth.admintoken')->post('/howitwork/create', [HowItWorksController::class, 'CreateHowItWorks']);
Route::middleware('auth.admintoken')->get('/howitwork/view', [HowItWorksController::class, 'ViewHowItWorks']);
Route::middleware('auth.admintoken')->put('/howitwork/edit/{id}', [HowItWorksController::class, 'EditHowItWorks']);
Route::middleware('auth.admintoken')->delete('/howitwork/delete/{id}', [HowItWorksController::class, 'DeleteHowItWorks']);

//Admin FAQ 
Route::middleware('auth.admintoken')->post('/faq/create', [FaqController::class, 'CreateFAQs']);
Route::middleware('auth.admintoken')->get('/faq/view', [FaqController::class, 'ViewFAQs']);
Route::middleware('auth.admintoken')->put('/faq/edit/{id}', [FaqController::class, 'EditFAQs']);
Route::middleware('auth.admintoken')->delete('/faq/delete/{id}', [FaqController::class, 'DeleteFAQs']);

//Admin Contact Us 
Route::post('/contactus/create', [ContactUsController::class, 'CreateContactUs']);
Route::middleware('auth.admintoken')->get('/contactus/view', [ContactUsController::class, 'ViewContactUs']);
Route::middleware('auth.admintoken')->put('/contactus/edit/{id}', [ContactUsController::class, 'EditContactUs']);

//Admin About Us
Route::middleware('auth.admintoken')->get('/aboutus/view', [AboutUsController::class, 'ViewAboutUs']);
Route::middleware('auth.admintoken')->put('/aboutus/edit/{id}', [AboutUsController::class, 'EditAboutUs']);

//Admin Terms & Conditions
Route::middleware('auth.admintoken')->get('/tandc/view', [TermsAndConditionController::class, 'ViewTermsandCondition']);
Route::middleware('auth.admintoken')->put('/tandc/edit/{id}', [TermsAndConditionController::class, 'EditTermsandCondition']);

//Admin Privacy & Policy
Route::middleware('auth.admintoken')->get('/pandp/view', [PrivacyAndPolicyController::class, 'ViewPrivacyandPolicy']);
Route::middleware('auth.admintoken')->put('/pandp/edit/{id}', [PrivacyAndPolicyController::class, 'EditPrivacyandPolicy']);

//Admin Disclaimer
Route::middleware('auth.admintoken')->get('/disclaimer/view', [DisclaimerController::class, 'ViewDisclaimer']);
Route::middleware('auth.admintoken')->put('/disclaimer/edit/{id}', [DisclaimerController::class, 'EditDisclaimer']);

//Admin News 
Route::middleware('auth.admintoken')->get('/news/view', [NewsController::class, 'ViewNews']);
Route::middleware('auth.admintoken')->put('/news/edit/{id}', [NewsController::class, 'EditNews']);

//Admin Feedback
Route::middleware('auth.admintoken')->get('/feedback/view', [Feedbackcontroller::class, 'ViewFeedbackForAdmin']);
Route::middleware('auth.admintoken')->post('/feedback/reply/{id}', [Feedbackcontroller::class, 'ReplyToFeedback']);
Route::middleware('auth.admintoken')->put('/feedback/edit/reply/{id}', [Feedbackcontroller::class, 'EditFeedbackReply']);
Route::middleware('auth.admintoken')->delete('/feedback/delete/reply/{id}', [Feedbackcontroller::class, 'DeleteFeedbackReply']);

//Admin Roles
Route::middleware('auth.admintoken')->post('/roles/create', [RolesController::class, 'CreateRoles']);
Route::middleware('auth.admintoken')->get('/roles/view', [RolesController::class, 'ViewRoles']);
Route::middleware('auth.admintoken')->put('/roles/edit/{id}', [RolesController::class, 'EditRoles']);
Route::middleware('auth.admintoken')->delete('/roles/delete/{id}', [RolesController::class, 'DeleteRoles']);

//Admin Permissions
Route::middleware('auth.admintoken')->post('/permissions/create', [PermissionController::class, 'CreatePermission']);
Route::middleware('auth.admintoken')->get('/permissions/view', [PermissionController::class, 'ViewPermission']);
Route::middleware('auth.admintoken')->put('/permissions/edit/{id}', [PermissionController::class, 'EditPermission']);
Route::middleware('auth.admintoken')->delete('/permissions/delete/{id}', [PermissionController::class, 'DeletePermission']);

//Admin Settings
Route::middleware('auth.admintoken')->get('/permissions/list/{id}', [SettingsController::class, 'ListOfPermission']);
Route::middleware('auth.admintoken')->post('/permissions/update', [SettingsController::class, 'UpdatePermission']);

//Admin Reports
Route::middleware('auth.admintoken')->post('/users/reports/pdf', [ReportsController::class, 'UserPdfReport']);
Route::get('/users/reports/excel', [ReportsController::class, 'UserExcelReport']);
Route::get('/users/reports/csv', [ReportsController::class, 'UserCsvReport']);
Route::middleware('auth.admintoken')->post('/reminders/reports/pdf', [ReportsController::class, 'ReminderPdfReport']);
Route::middleware('auth.admintoken')->get('/reminders/excel', [ReportsController::class, 'ReminderExcelReport']);
Route::middleware('auth.admintoken')->get('/reminders/csv', [ReportsController::class, 'ReminderCsvReport']);