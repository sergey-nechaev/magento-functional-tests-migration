<?php
namespace Magento\AcceptanceTest\_default\Backend;

use Magento\FunctionalTestingFramework\AcceptanceTester;
use \Codeception\Util\Locator;
use Yandex\Allure\Adapter\Annotation\Features;
use Yandex\Allure\Adapter\Annotation\Stories;
use Yandex\Allure\Adapter\Annotation\Title;
use Yandex\Allure\Adapter\Annotation\Description;
use Yandex\Allure\Adapter\Annotation\Parameter;
use Yandex\Allure\Adapter\Annotation\Severity;
use Yandex\Allure\Adapter\Model\SeverityLevel;
use Yandex\Allure\Adapter\Annotation\TestCaseId;

/**
 * @Title("[NO TESTCASEID]: Reorder Order from Admin for Offline Payment Methods")
 * @Description("Reorder placed order (update products, billing address).<h3>Test files</h3>app/code/Magento/Sales/Test/Mftf/Test/AdminReorderOrderEntityTest.xml<br>")
 * @group Sales, mftf_migrated
 */
class AdminReorderOrderEntityTestCest
{
	/**
	  * @param AcceptanceTester $I
	  * @throws \Exception
	  */
	public function _before(AcceptanceTester $I)
	{
		$enableFlatRate = $I->magentoCLI("config:set carriers/flatrate/active 1", 60); // stepKey: enableFlatRate
		$I->comment($enableFlatRate);
		$enableCashOnDelivery = $I->magentoCLI("config:set payment/cashondelivery/active 1", 60); // stepKey: enableCashOnDelivery
		$I->comment($enableCashOnDelivery);
		$I->createEntity("simpleCustomer", "hook", "Simple_US_Customer_Two_Addresses", [], []); // stepKey: simpleCustomer
		$simpleProduct1Fields['price'] = "50.99";
		$I->createEntity("simpleProduct1", "hook", "SimpleProduct2", [], $simpleProduct1Fields); // stepKey: simpleProduct1
		$simpleProduct2Fields['price'] = "60.99";
		$I->createEntity("simpleProduct2", "hook", "SimpleProduct2", [], $simpleProduct2Fields); // stepKey: simpleProduct2
		$I->createEntity("createCartPriceRule", "hook", "ApiSalesRule", [], []); // stepKey: createCartPriceRule
		$I->createEntity("createCouponForCartPriceRule", "hook", "SimpleSalesRuleCoupon", ["createCartPriceRule"], []); // stepKey: createCouponForCartPriceRule
		$I->comment("Entering Action Group [loginAsAdmin] LoginAsAdmin");
		$I->amOnPage("/" . getenv("MAGENTO_BACKEND_NAME") . "/admin"); // stepKey: navigateToAdminLoginAsAdmin
		$I->fillField("#username", getenv("MAGENTO_ADMIN_USERNAME")); // stepKey: fillUsernameLoginAsAdmin
		$I->fillField("#login", getenv("MAGENTO_ADMIN_PASSWORD")); // stepKey: fillPasswordLoginAsAdmin
		$I->click(".actions .action-primary"); // stepKey: clickLoginLoginAsAdmin
		$I->waitForPageLoad(30); // stepKey: clickLoginLoginAsAdminWaitForPageLoad
		$I->conditionalClick(".modal-popup .action-secondary", ".modal-popup .action-secondary", true); // stepKey: clickDontAllowButtonIfVisibleLoginAsAdmin
		$I->closeAdminNotification(); // stepKey: closeAdminNotificationLoginAsAdmin
		$I->comment("Exiting Action Group [loginAsAdmin] LoginAsAdmin");
		$runCronIndex = $I->magentoCron("index", 90); // stepKey: runCronIndex
		$I->comment($runCronIndex);
	}

	/**
	  * @param AcceptanceTester $I
	  * @throws \Exception
	  */
	public function _after(AcceptanceTester $I)
	{
		$disableFlatRate = $I->magentoCLI("config:set carriers/flatrate/active 0", 60); // stepKey: disableFlatRate
		$I->comment($disableFlatRate);
		$disableCashOnDelivery = $I->magentoCLI("config:set payment/cashondelivery/active 0", 60); // stepKey: disableCashOnDelivery
		$I->comment($disableCashOnDelivery);
		$I->deleteEntity("simpleCustomer", "hook"); // stepKey: deleteSimpleCustomer
		$I->deleteEntity("simpleProduct1", "hook"); // stepKey: deleteSimpleProduct1
		$I->deleteEntity("simpleProduct2", "hook"); // stepKey: deleteSimpleProduct2
		$I->deleteEntity("createCartPriceRule", "hook"); // stepKey: deleteCartPriceRule
		$I->comment("Entering Action Group [adminLogout] AdminLogoutActionGroup");
		$I->amOnPage("/" . getenv("MAGENTO_BACKEND_NAME") . "/admin/auth/logout/"); // stepKey: amOnLogoutPageAdminLogout
		$I->comment("Exiting Action Group [adminLogout] AdminLogoutActionGroup");
	}

	/**
	  * @param AcceptanceTester $I
	  * @throws \Exception
	  */
	public function _failed(AcceptanceTester $I)
	{
		$I->saveScreenshot(); // stepKey: saveScreenshot
	}

	/**
	 * @Stories({"Reorder Order from Admin for Offline Payment Methods"})
	 * @Features({"Sales"})
	 * @Severity(level = SeverityLevel::NORMAL)
	 * @Parameter(name = "AcceptanceTester", value="$I")
	 * @param AcceptanceTester $I
	 * @return void
	 * @throws \Exception
	 */
	public function AdminReorderOrderEntityTest(AcceptanceTester $I)
	{
		$I->comment("Entering Action Group [navigateToNewOrderWithExistingCustomer] NavigateToNewOrderPageExistingCustomerActionGroup");
		$I->amOnPage("/" . getenv("MAGENTO_BACKEND_NAME") . "/sales/order/"); // stepKey: navigateToOrderIndexPageNavigateToNewOrderWithExistingCustomer
		$I->waitForPageLoad(30); // stepKey: waitForIndexPageLoadNavigateToNewOrderWithExistingCustomer
		$I->see("Orders", ".page-header h1.page-title"); // stepKey: seeIndexPageTitleNavigateToNewOrderWithExistingCustomer
		$I->click(".page-actions-buttons button#add"); // stepKey: clickCreateNewOrderNavigateToNewOrderWithExistingCustomer
		$I->waitForPageLoad(30); // stepKey: clickCreateNewOrderNavigateToNewOrderWithExistingCustomerWaitForPageLoad
		$I->waitForPageLoad(30); // stepKey: waitForCustomerGridLoadNavigateToNewOrderWithExistingCustomer
		$I->comment("Clear grid filters");
		$I->conditionalClick("#sales_order_create_customer_grid [data-action='grid-filter-reset']", "#sales_order_create_customer_grid [data-action='grid-filter-reset']", true); // stepKey: clearExistingCustomerFiltersNavigateToNewOrderWithExistingCustomer
		$I->waitForPageLoad(30); // stepKey: clearExistingCustomerFiltersNavigateToNewOrderWithExistingCustomerWaitForPageLoad
		$I->fillField("#sales_order_create_customer_grid_filter_email", $I->retrieveEntityField('simpleCustomer', 'email', 'test')); // stepKey: filterEmailNavigateToNewOrderWithExistingCustomer
		$I->click(".action-secondary[title='Search']"); // stepKey: applyFilterNavigateToNewOrderWithExistingCustomer
		$I->waitForPageLoad(30); // stepKey: waitForFilteredCustomerGridLoadNavigateToNewOrderWithExistingCustomer
		$I->click("tr:nth-of-type(1)[data-role='row']"); // stepKey: clickOnCustomerNavigateToNewOrderWithExistingCustomer
		$I->waitForPageLoad(30); // stepKey: waitForCreateOrderPageLoadNavigateToNewOrderWithExistingCustomer
		$I->comment("Select store view if appears");
		$I->conditionalClick("//div[contains(@class, 'tree-store-scope')]//label[contains(text(), 'Default Store View')]/preceding-sibling::input", "//div[contains(@class, 'tree-store-scope')]//label[contains(text(), 'Default Store View')]/preceding-sibling::input", true); // stepKey: selectStoreViewIfAppearsNavigateToNewOrderWithExistingCustomer
		$I->waitForPageLoad(30); // stepKey: selectStoreViewIfAppearsNavigateToNewOrderWithExistingCustomerWaitForPageLoad
		$I->waitForPageLoad(30); // stepKey: waitForCreateOrderPageLoadAfterStoreSelectNavigateToNewOrderWithExistingCustomer
		$I->see("Create New Order", ".page-header h1.page-title"); // stepKey: seeNewOrderPageTitleNavigateToNewOrderWithExistingCustomer
		$I->comment("Exiting Action Group [navigateToNewOrderWithExistingCustomer] NavigateToNewOrderPageExistingCustomerActionGroup");
		$I->comment("Entering Action Group [addSimpleProduct1ToOrder] AddSimpleProductToOrderActionGroup");
		$I->click("//section[@id='order-items']/div/div/button/span[text() = 'Add Products']"); // stepKey: clickAddProductsAddSimpleProduct1ToOrder
		$I->waitForPageLoad(30); // stepKey: clickAddProductsAddSimpleProduct1ToOrderWaitForPageLoad
		$I->fillField("#sales_order_create_search_grid_filter_sku", $I->retrieveEntityField('simpleProduct1', 'sku', 'test')); // stepKey: fillSkuFilterAddSimpleProduct1ToOrder
		$I->click("#sales_order_create_search_grid [data-action='grid-filter-apply']"); // stepKey: clickSearchAddSimpleProduct1ToOrder
		$I->waitForPageLoad(30); // stepKey: clickSearchAddSimpleProduct1ToOrderWaitForPageLoad
		$I->scrollTo("#sales_order_create_search_grid_table > tbody tr:nth-of-type(1) td.col-select [type=checkbox]", 0, -100); // stepKey: scrollToCheckColumnAddSimpleProduct1ToOrder
		$I->checkOption("#sales_order_create_search_grid_table > tbody tr:nth-of-type(1) td.col-select [type=checkbox]"); // stepKey: selectProductAddSimpleProduct1ToOrder
		$I->fillField("#sales_order_create_search_grid_table > tbody tr:nth-of-type(1) td.col-qty [name='qty']", "1"); // stepKey: fillProductQtyAddSimpleProduct1ToOrder
		$I->scrollTo("#order-search .admin__page-section-title .actions button.action-add", 0, -100); // stepKey: scrollToAddSelectedButtonAddSimpleProduct1ToOrder
		$I->waitForPageLoad(30); // stepKey: scrollToAddSelectedButtonAddSimpleProduct1ToOrderWaitForPageLoad
		$I->click("#order-search .admin__page-section-title .actions button.action-add"); // stepKey: clickAddSelectedProductsAddSimpleProduct1ToOrder
		$I->waitForPageLoad(30); // stepKey: clickAddSelectedProductsAddSimpleProduct1ToOrderWaitForPageLoad
		$I->wait(5); // stepKey: waitForOptionsToLoadAddSimpleProduct1ToOrder
		$I->comment("Exiting Action Group [addSimpleProduct1ToOrder] AddSimpleProductToOrderActionGroup");
		$I->comment("Entering Action Group [addSimpleProduct2ToOrder] AddSimpleProductToOrderActionGroup");
		$I->click("//section[@id='order-items']/div/div/button/span[text() = 'Add Products']"); // stepKey: clickAddProductsAddSimpleProduct2ToOrder
		$I->waitForPageLoad(30); // stepKey: clickAddProductsAddSimpleProduct2ToOrderWaitForPageLoad
		$I->fillField("#sales_order_create_search_grid_filter_sku", $I->retrieveEntityField('simpleProduct2', 'sku', 'test')); // stepKey: fillSkuFilterAddSimpleProduct2ToOrder
		$I->click("#sales_order_create_search_grid [data-action='grid-filter-apply']"); // stepKey: clickSearchAddSimpleProduct2ToOrder
		$I->waitForPageLoad(30); // stepKey: clickSearchAddSimpleProduct2ToOrderWaitForPageLoad
		$I->scrollTo("#sales_order_create_search_grid_table > tbody tr:nth-of-type(1) td.col-select [type=checkbox]", 0, -100); // stepKey: scrollToCheckColumnAddSimpleProduct2ToOrder
		$I->checkOption("#sales_order_create_search_grid_table > tbody tr:nth-of-type(1) td.col-select [type=checkbox]"); // stepKey: selectProductAddSimpleProduct2ToOrder
		$I->fillField("#sales_order_create_search_grid_table > tbody tr:nth-of-type(1) td.col-qty [name='qty']", "1"); // stepKey: fillProductQtyAddSimpleProduct2ToOrder
		$I->scrollTo("#order-search .admin__page-section-title .actions button.action-add", 0, -100); // stepKey: scrollToAddSelectedButtonAddSimpleProduct2ToOrder
		$I->waitForPageLoad(30); // stepKey: scrollToAddSelectedButtonAddSimpleProduct2ToOrderWaitForPageLoad
		$I->click("#order-search .admin__page-section-title .actions button.action-add"); // stepKey: clickAddSelectedProductsAddSimpleProduct2ToOrder
		$I->waitForPageLoad(30); // stepKey: clickAddSelectedProductsAddSimpleProduct2ToOrderWaitForPageLoad
		$I->wait(5); // stepKey: waitForOptionsToLoadAddSimpleProduct2ToOrder
		$I->comment("Exiting Action Group [addSimpleProduct2ToOrder] AddSimpleProductToOrderActionGroup");
		$I->comment("Entering Action Group [selectPaymentMethod] SelectCheckMoneyPaymentMethodActionGroup");
		$I->waitForElementVisible("#order-billing_method", 30); // stepKey: waitForPaymentOptionsSelectPaymentMethod
		$I->conditionalClick("#p_method_checkmo", "#p_method_checkmo", true); // stepKey: checkCheckMoneyOptionSelectPaymentMethod
		$I->waitForPageLoad(30); // stepKey: checkCheckMoneyOptionSelectPaymentMethodWaitForPageLoad
		$I->comment("Exiting Action Group [selectPaymentMethod] SelectCheckMoneyPaymentMethodActionGroup");
		$I->comment("Entering Action Group [orderSelectFlatRateShippingMethod] OrderSelectFlatRateShippingActionGroup");
		$I->click("#order-methods span.title"); // stepKey: unfocusOrderSelectFlatRateShippingMethod
		$I->waitForPageLoad(30); // stepKey: waitForJavascriptToFinishOrderSelectFlatRateShippingMethod
		$I->click("#order-shipping_method a.action-default"); // stepKey: clickShippingMethodsOrderSelectFlatRateShippingMethod
		$I->waitForPageLoad(30); // stepKey: clickShippingMethodsOrderSelectFlatRateShippingMethodWaitForPageLoad
		$I->waitForElementVisible("#s_method_flatrate_flatrate", 30); // stepKey: waitForShippingOptionsOrderSelectFlatRateShippingMethod
		$I->waitForPageLoad(30); // stepKey: waitForShippingOptionsOrderSelectFlatRateShippingMethodWaitForPageLoad
		$I->selectOption("#s_method_flatrate_flatrate", "flatrate_flatrate"); // stepKey: checkFlatRateOrderSelectFlatRateShippingMethod
		$I->waitForPageLoad(30); // stepKey: checkFlatRateOrderSelectFlatRateShippingMethodWaitForPageLoad
		$I->comment("Exiting Action Group [orderSelectFlatRateShippingMethod] OrderSelectFlatRateShippingActionGroup");
		$I->comment("Entering Action Group [applyCoupon] AdminApplyCouponToOrderActionGroup");
		$I->fillField("#order-coupons input", $I->retrieveEntityField('createCouponForCartPriceRule', 'code', 'test')); // stepKey: fillCouponCodeApplyCoupon
		$I->waitForPageLoad(30); // stepKey: fillCouponCodeApplyCouponWaitForPageLoad
		$I->click("#order-coupons button"); // stepKey: applyCouponApplyCoupon
		$I->waitForPageLoad(30); // stepKey: waitForApplyingCouponApplyCoupon
		$I->see("The coupon code has been accepted.", "div.message-success"); // stepKey: seeSuccessMessageApplyCoupon
		$I->comment("Exiting Action Group [applyCoupon] AdminApplyCouponToOrderActionGroup");
		$I->comment("Entering Action Group [submitOrder] AdminSubmitOrderActionGroup");
		$I->click("#submit_order_top_button"); // stepKey: submitOrderSubmitOrder
		$I->waitForPageLoad(60); // stepKey: submitOrderSubmitOrderWaitForPageLoad
		$I->waitForPageLoad(30); // stepKey: waitForPageLoadSubmitOrder
		$I->see("You created the order."); // stepKey: seeSuccessMessageForOrderSubmitOrder
		$I->comment("Exiting Action Group [submitOrder] AdminSubmitOrderActionGroup");
		$I->comment("Entering Action Group [verifyCreatedOrderInformation] VerifyCreatedOrderInformationActionGroup");
		$I->see("You created the order.", "div.message-success"); // stepKey: seeSuccessMessageVerifyCreatedOrderInformation
		$I->see("Pending", ".order-information table.order-information-table #order_status"); // stepKey: seeOrderPendingStatusVerifyCreatedOrderInformation
		$getOrderIdVerifyCreatedOrderInformation = $I->grabTextFrom("|Order # (\d+)|"); // stepKey: getOrderIdVerifyCreatedOrderInformation
		$I->assertNotEmpty($getOrderIdVerifyCreatedOrderInformation); // stepKey: assertOrderIdIsNotEmptyVerifyCreatedOrderInformation
		$I->comment("Exiting Action Group [verifyCreatedOrderInformation] VerifyCreatedOrderInformationActionGroup");
		$getOrderId = $I->grabTextFrom("|Order # (\d+)|"); // stepKey: getOrderId
		$I->comment("Entering Action Group [verifyAllButtonsAvailable] AssertOrderButtonsAvailableActionGroup");
		$I->seeElement("#back"); // stepKey: seeBackBtnVerifyAllButtonsAvailable
		$I->waitForPageLoad(30); // stepKey: seeBackBtnVerifyAllButtonsAvailableWaitForPageLoad
		$I->seeElement("#order_reorder"); // stepKey: seeReorderBtnVerifyAllButtonsAvailable
		$I->waitForPageLoad(30); // stepKey: seeReorderBtnVerifyAllButtonsAvailableWaitForPageLoad
		$I->seeElement("#order-view-cancel-button"); // stepKey: seeCancelBtnVerifyAllButtonsAvailable
		$I->waitForPageLoad(30); // stepKey: seeCancelBtnVerifyAllButtonsAvailableWaitForPageLoad
		$I->seeElement("#send_notification"); // stepKey: seeSendEmailBtnVerifyAllButtonsAvailable
		$I->waitForPageLoad(30); // stepKey: seeSendEmailBtnVerifyAllButtonsAvailableWaitForPageLoad
		$I->seeElement("#order-view-hold-button"); // stepKey: seeHoldBtnVerifyAllButtonsAvailable
		$I->waitForPageLoad(30); // stepKey: seeHoldBtnVerifyAllButtonsAvailableWaitForPageLoad
		$I->seeElement("#order_invoice"); // stepKey: seeInvoiceBtnVerifyAllButtonsAvailable
		$I->waitForPageLoad(30); // stepKey: seeInvoiceBtnVerifyAllButtonsAvailableWaitForPageLoad
		$I->seeElement("#order_ship"); // stepKey: seeShipBtnVerifyAllButtonsAvailable
		$I->waitForPageLoad(30); // stepKey: seeShipBtnVerifyAllButtonsAvailableWaitForPageLoad
		$I->seeElement("#order_edit"); // stepKey: seeEditBtnVerifyAllButtonsAvailable
		$I->waitForPageLoad(30); // stepKey: seeEditBtnVerifyAllButtonsAvailableWaitForPageLoad
		$I->comment("Exiting Action Group [verifyAllButtonsAvailable] AssertOrderButtonsAvailableActionGroup");
		$I->comment("Entering Action Group [reorderOrder] AdminOpenReorderPageActionGroup");
		$I->click("#order_reorder"); // stepKey: clickReorderReorderOrder
		$I->waitForPageLoad(30); // stepKey: clickReorderReorderOrderWaitForPageLoad
		$I->waitForPageLoad(30); // stepKey: waitForPageLoadReorderOrder
		$I->comment("Exiting Action Group [reorderOrder] AdminOpenReorderPageActionGroup");
		$I->comment("Entering Action Group [checkPageTitle] AdminAssertPageTitleActionGroup");
		$I->see("Create New Order", ".page-title-wrapper h1"); // stepKey: assertPageTitleCheckPageTitle
		$I->comment("Exiting Action Group [checkPageTitle] AdminAssertPageTitleActionGroup");
		$I->comment("Entering Action Group [addOneMoreSimpleProduct1ToOrder] AddSimpleProductToOrderActionGroup");
		$I->click("//section[@id='order-items']/div/div/button/span[text() = 'Add Products']"); // stepKey: clickAddProductsAddOneMoreSimpleProduct1ToOrder
		$I->waitForPageLoad(30); // stepKey: clickAddProductsAddOneMoreSimpleProduct1ToOrderWaitForPageLoad
		$I->fillField("#sales_order_create_search_grid_filter_sku", $I->retrieveEntityField('simpleProduct1', 'sku', 'test')); // stepKey: fillSkuFilterAddOneMoreSimpleProduct1ToOrder
		$I->click("#sales_order_create_search_grid [data-action='grid-filter-apply']"); // stepKey: clickSearchAddOneMoreSimpleProduct1ToOrder
		$I->waitForPageLoad(30); // stepKey: clickSearchAddOneMoreSimpleProduct1ToOrderWaitForPageLoad
		$I->scrollTo("#sales_order_create_search_grid_table > tbody tr:nth-of-type(1) td.col-select [type=checkbox]", 0, -100); // stepKey: scrollToCheckColumnAddOneMoreSimpleProduct1ToOrder
		$I->checkOption("#sales_order_create_search_grid_table > tbody tr:nth-of-type(1) td.col-select [type=checkbox]"); // stepKey: selectProductAddOneMoreSimpleProduct1ToOrder
		$I->fillField("#sales_order_create_search_grid_table > tbody tr:nth-of-type(1) td.col-qty [name='qty']", "1"); // stepKey: fillProductQtyAddOneMoreSimpleProduct1ToOrder
		$I->scrollTo("#order-search .admin__page-section-title .actions button.action-add", 0, -100); // stepKey: scrollToAddSelectedButtonAddOneMoreSimpleProduct1ToOrder
		$I->waitForPageLoad(30); // stepKey: scrollToAddSelectedButtonAddOneMoreSimpleProduct1ToOrderWaitForPageLoad
		$I->click("#order-search .admin__page-section-title .actions button.action-add"); // stepKey: clickAddSelectedProductsAddOneMoreSimpleProduct1ToOrder
		$I->waitForPageLoad(30); // stepKey: clickAddSelectedProductsAddOneMoreSimpleProduct1ToOrderWaitForPageLoad
		$I->wait(5); // stepKey: waitForOptionsToLoadAddOneMoreSimpleProduct1ToOrder
		$I->comment("Exiting Action Group [addOneMoreSimpleProduct1ToOrder] AddSimpleProductToOrderActionGroup");
		$I->comment("Entering Action Group [changeBillingAddress] AdminChangeBillingAddressOnCreateOrderPageActionGroup");
		$I->selectOption("//select[@id='order-billing_address_customer_address_id']", "New York"); // stepKey: selectAddressChangeBillingAddress
		$I->waitForPageLoad(30); // stepKey: waitForChangeOfBillingAddressChangeBillingAddress
		$I->waitForPageLoad(30); // stepKey: waitForUpdateOfPaymentMethodsChangeBillingAddress
		$I->comment("Exiting Action Group [changeBillingAddress] AdminChangeBillingAddressOnCreateOrderPageActionGroup");
		$I->comment("Entering Action Group [selectFlatRateShippingMethodForReorder] OrderSelectFlatRateShippingActionGroup");
		$I->click("#order-methods span.title"); // stepKey: unfocusSelectFlatRateShippingMethodForReorder
		$I->waitForPageLoad(30); // stepKey: waitForJavascriptToFinishSelectFlatRateShippingMethodForReorder
		$I->click("#order-shipping_method a.action-default"); // stepKey: clickShippingMethodsSelectFlatRateShippingMethodForReorder
		$I->waitForPageLoad(30); // stepKey: clickShippingMethodsSelectFlatRateShippingMethodForReorderWaitForPageLoad
		$I->waitForElementVisible("#s_method_flatrate_flatrate", 30); // stepKey: waitForShippingOptionsSelectFlatRateShippingMethodForReorder
		$I->waitForPageLoad(30); // stepKey: waitForShippingOptionsSelectFlatRateShippingMethodForReorderWaitForPageLoad
		$I->selectOption("#s_method_flatrate_flatrate", "flatrate_flatrate"); // stepKey: checkFlatRateSelectFlatRateShippingMethodForReorder
		$I->waitForPageLoad(30); // stepKey: checkFlatRateSelectFlatRateShippingMethodForReorderWaitForPageLoad
		$I->comment("Exiting Action Group [selectFlatRateShippingMethodForReorder] OrderSelectFlatRateShippingActionGroup");
		$I->comment("Entering Action Group [selectPaymentMethodForReorder] SelectCheckMoneyPaymentMethodActionGroup");
		$I->waitForElementVisible("#order-billing_method", 30); // stepKey: waitForPaymentOptionsSelectPaymentMethodForReorder
		$I->conditionalClick("#p_method_checkmo", "#p_method_checkmo", true); // stepKey: checkCheckMoneyOptionSelectPaymentMethodForReorder
		$I->waitForPageLoad(30); // stepKey: checkCheckMoneyOptionSelectPaymentMethodForReorderWaitForPageLoad
		$I->comment("Exiting Action Group [selectPaymentMethodForReorder] SelectCheckMoneyPaymentMethodActionGroup");
		$I->comment("Entering Action Group [submitReorder] AdminSubmitOrderActionGroup");
		$I->click("#submit_order_top_button"); // stepKey: submitOrderSubmitReorder
		$I->waitForPageLoad(60); // stepKey: submitOrderSubmitReorderWaitForPageLoad
		$I->waitForPageLoad(30); // stepKey: waitForPageLoadSubmitReorder
		$I->see("You created the order."); // stepKey: seeSuccessMessageForOrderSubmitReorder
		$I->comment("Exiting Action Group [submitReorder] AdminSubmitOrderActionGroup");
		$I->comment("Entering Action Group [verifyCreatedReorderInformation] VerifyCreatedOrderInformationActionGroup");
		$I->see("You created the order.", "div.message-success"); // stepKey: seeSuccessMessageVerifyCreatedReorderInformation
		$I->see("Pending", ".order-information table.order-information-table #order_status"); // stepKey: seeOrderPendingStatusVerifyCreatedReorderInformation
		$getOrderIdVerifyCreatedReorderInformation = $I->grabTextFrom("|Order # (\d+)|"); // stepKey: getOrderIdVerifyCreatedReorderInformation
		$I->assertNotEmpty($getOrderIdVerifyCreatedReorderInformation); // stepKey: assertOrderIdIsNotEmptyVerifyCreatedReorderInformation
		$I->comment("Exiting Action Group [verifyCreatedReorderInformation] VerifyCreatedOrderInformationActionGroup");
		$getReorderId = $I->grabTextFrom("|Order # (\d+)|"); // stepKey: getReorderId
		$I->comment("Entering Action Group [verifyAllButtonsAvailableForReorder] AssertOrderButtonsAvailableActionGroup");
		$I->seeElement("#back"); // stepKey: seeBackBtnVerifyAllButtonsAvailableForReorder
		$I->waitForPageLoad(30); // stepKey: seeBackBtnVerifyAllButtonsAvailableForReorderWaitForPageLoad
		$I->seeElement("#order_reorder"); // stepKey: seeReorderBtnVerifyAllButtonsAvailableForReorder
		$I->waitForPageLoad(30); // stepKey: seeReorderBtnVerifyAllButtonsAvailableForReorderWaitForPageLoad
		$I->seeElement("#order-view-cancel-button"); // stepKey: seeCancelBtnVerifyAllButtonsAvailableForReorder
		$I->waitForPageLoad(30); // stepKey: seeCancelBtnVerifyAllButtonsAvailableForReorderWaitForPageLoad
		$I->seeElement("#send_notification"); // stepKey: seeSendEmailBtnVerifyAllButtonsAvailableForReorder
		$I->waitForPageLoad(30); // stepKey: seeSendEmailBtnVerifyAllButtonsAvailableForReorderWaitForPageLoad
		$I->seeElement("#order-view-hold-button"); // stepKey: seeHoldBtnVerifyAllButtonsAvailableForReorder
		$I->waitForPageLoad(30); // stepKey: seeHoldBtnVerifyAllButtonsAvailableForReorderWaitForPageLoad
		$I->seeElement("#order_invoice"); // stepKey: seeInvoiceBtnVerifyAllButtonsAvailableForReorder
		$I->waitForPageLoad(30); // stepKey: seeInvoiceBtnVerifyAllButtonsAvailableForReorderWaitForPageLoad
		$I->seeElement("#order_ship"); // stepKey: seeShipBtnVerifyAllButtonsAvailableForReorder
		$I->waitForPageLoad(30); // stepKey: seeShipBtnVerifyAllButtonsAvailableForReorderWaitForPageLoad
		$I->seeElement("#order_edit"); // stepKey: seeEditBtnVerifyAllButtonsAvailableForReorder
		$I->waitForPageLoad(30); // stepKey: seeEditBtnVerifyAllButtonsAvailableForReorderWaitForPageLoad
		$I->comment("Exiting Action Group [verifyAllButtonsAvailableForReorder] AssertOrderButtonsAvailableActionGroup");
		$I->comment("Entering Action Group [verifyOrderStatusAndGrandTotal] AssertAdminCheckOrderStatusAndGrandTotalInOrdersGridActionGroup");
		$I->amOnPage("/" . getenv("MAGENTO_BACKEND_NAME") . "/sales/order/"); // stepKey: navigateToOrderGridPageVerifyOrderStatusAndGrandTotal
		$I->waitForPageLoad(30); // stepKey: waitForOrdersPageVerifyOrderStatusAndGrandTotal
		$I->conditionalClick(".admin__data-grid-header [data-action='grid-filter-reset']", ".admin__data-grid-header [data-action='grid-filter-reset']", true); // stepKey: clearExistingOrderFiltersVerifyOrderStatusAndGrandTotal
		$I->waitForPageLoad(30); // stepKey: clearExistingOrderFiltersVerifyOrderStatusAndGrandTotalWaitForPageLoad
		$I->waitForPageLoad(30); // stepKey: waitForClearFiltersVerifyOrderStatusAndGrandTotal
		$I->click("button[data-action='grid-filter-expand']"); // stepKey: openOrderGridFiltersVerifyOrderStatusAndGrandTotal
		$I->waitForPageLoad(30); // stepKey: openOrderGridFiltersVerifyOrderStatusAndGrandTotalWaitForPageLoad
		$I->waitForPageLoad(30); // stepKey: waitForClickFiltersVerifyOrderStatusAndGrandTotal
		$I->fillField(".admin__data-grid-filters input[name='increment_id']", "$getOrderId"); // stepKey: fillOrderIdFilterVerifyOrderStatusAndGrandTotal
		$I->click("button[data-action='grid-filter-apply']"); // stepKey: clickOrderApplyFiltersVerifyOrderStatusAndGrandTotal
		$I->waitForPageLoad(30); // stepKey: clickOrderApplyFiltersVerifyOrderStatusAndGrandTotalWaitForPageLoad
		$I->waitForPageLoad(30); // stepKey: waitForApplyFiltersVerifyOrderStatusAndGrandTotal
		$I->see("Pending", "//tr[1]//td[count(//div[@data-role='grid-wrapper']//tr//th[contains(., 'Status')]/preceding-sibling::th) +1 ]"); // stepKey: assertOrderStatusVerifyOrderStatusAndGrandTotal
		$I->see("110.78", "//tr[1]//td[count(//div[@data-role='grid-wrapper']//tr//th[contains(., 'Grand Total (Purchased)')]/preceding-sibling::th) +1 ]"); // stepKey: assertOrderGrandTotalVerifyOrderStatusAndGrandTotal
		$I->comment("Exiting Action Group [verifyOrderStatusAndGrandTotal] AssertAdminCheckOrderStatusAndGrandTotalInOrdersGridActionGroup");
		$I->comment("Entering Action Group [verifyReorderStatusAndGrandTotal] AssertAdminCheckOrderStatusAndGrandTotalInOrdersGridActionGroup");
		$I->amOnPage("/" . getenv("MAGENTO_BACKEND_NAME") . "/sales/order/"); // stepKey: navigateToOrderGridPageVerifyReorderStatusAndGrandTotal
		$I->waitForPageLoad(30); // stepKey: waitForOrdersPageVerifyReorderStatusAndGrandTotal
		$I->conditionalClick(".admin__data-grid-header [data-action='grid-filter-reset']", ".admin__data-grid-header [data-action='grid-filter-reset']", true); // stepKey: clearExistingOrderFiltersVerifyReorderStatusAndGrandTotal
		$I->waitForPageLoad(30); // stepKey: clearExistingOrderFiltersVerifyReorderStatusAndGrandTotalWaitForPageLoad
		$I->waitForPageLoad(30); // stepKey: waitForClearFiltersVerifyReorderStatusAndGrandTotal
		$I->click("button[data-action='grid-filter-expand']"); // stepKey: openOrderGridFiltersVerifyReorderStatusAndGrandTotal
		$I->waitForPageLoad(30); // stepKey: openOrderGridFiltersVerifyReorderStatusAndGrandTotalWaitForPageLoad
		$I->waitForPageLoad(30); // stepKey: waitForClickFiltersVerifyReorderStatusAndGrandTotal
		$I->fillField(".admin__data-grid-filters input[name='increment_id']", "$getReorderId"); // stepKey: fillOrderIdFilterVerifyReorderStatusAndGrandTotal
		$I->click("button[data-action='grid-filter-apply']"); // stepKey: clickOrderApplyFiltersVerifyReorderStatusAndGrandTotal
		$I->waitForPageLoad(30); // stepKey: clickOrderApplyFiltersVerifyReorderStatusAndGrandTotalWaitForPageLoad
		$I->waitForPageLoad(30); // stepKey: waitForApplyFiltersVerifyReorderStatusAndGrandTotal
		$I->see("Pending", "//tr[1]//td[count(//div[@data-role='grid-wrapper']//tr//th[contains(., 'Status')]/preceding-sibling::th) +1 ]"); // stepKey: assertOrderStatusVerifyReorderStatusAndGrandTotal
		$I->see("161.67", "//tr[1]//td[count(//div[@data-role='grid-wrapper']//tr//th[contains(., 'Grand Total (Purchased)')]/preceding-sibling::th) +1 ]"); // stepKey: assertOrderGrandTotalVerifyReorderStatusAndGrandTotal
		$I->comment("Exiting Action Group [verifyReorderStatusAndGrandTotal] AssertAdminCheckOrderStatusAndGrandTotalInOrdersGridActionGroup");
	}
}
