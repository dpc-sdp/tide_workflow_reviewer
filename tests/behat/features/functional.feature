@dpc-sdp @api
Feature: view fields, extra fields, views and access
  Ensure that the module meets the AC
  Scenario: Check if workbench_reviewer exists
    Given sites terms:
      | name                 | parent          | tid   | uuid                                  |
      | Test Site 1          | 0               | 10010 | 11dede11-10c0-111e1-1100-000000000031 |
      | Test Section 11      | Test Site 1     | 10011 | 11dede11-10d0-111e1-1100-000000000032 |
      | Test Sub Section 111 | Test Section 11 | 10012 | 11dede11-10e0-111e1-1100-000000000032 |
      | Test Sub Section 112 | Test Section 11 | 10013 | 11dede11-10f0-111e1-1100-000000000034 |
      | Test Section 12      | Test Site 1     | 10014 | 11dede11-10g0-111e1-1100-000000000035 |
      | Test Site 2          | 0               | 10015 | 11dede11-10h0-111e1-1100-000000000036 |
      | Test Site 3          | 0               | 10016 | 11dede11-10i0-111e1-1100-000000000037 |

    And topic terms:
      | name         | parent | tid   |
      | Test topic 1 | 0      | 10017 |
      | Test topic 2 | 0      | 10018 |
      | Test topic 3 | 0      | 10019 |

    And users:
      | name          | status | uid    | mail                      | pass         | field_user_site | roles      |
      | test.editor   |      1 | 999999 | test.editor@example.com   | L9dx9IJz3'M* | Test Section 11 | Editor     |
      | test.admin    |      1 | 999998 | site.admin@example.com    | L9dx9IJz3'M* | Test Section 11 | Site Admin |
      | test.approver |      1 | 999997 | test.approver@example.com | L9dx9IJz3'M* | Test Section 11 | Approver   |

    And landing_page content:
      | title       | path       | moderation_state | uuid                                | field_node_site                                             | field_node_primary_site | nid     | field_topic  | workbench_reviewer |
      | [TEST] LP 1 | /test-lp-1 | needs_review     | 99999999-aaaa-bbbb-ccc-000000000001 | Test Site 1, Test Section 11                                | Test Site 1             | 999999  | Test topic 1 | 999999             |
      | [TEST] LP 2 | /test-lp-2 | needs_review     | 99999999-aaaa-bbbb-ccc-000000000002 | Test Site 1, Test Section 11, Test Section 12               | Test Site 1             | 999998  | Test topic 2 | 999999             |
      | [TEST] LP 3 | /test-lp-3 | needs_review     | 99999999-aaaa-bbbb-ccc-000000000003 | Test Sub Section 111, Test Sub Section 112, Test Section 11 | Test Section 11         | 999997  | Test topic 3 | 999998             |
      | [TEST] LP 4 | /test-lp-4 | needs_review     | 99999999-aaaa-bbbb-ccc-000000000004 | Test Sub Section 111, Test Sub Section 112, Test Section 11 | Test Section 11         | 999996  | Test topic 3 | 999997             |

    When I am logged in as "test.editor"
    Then I go to "/admin/content/assigned-to-me"
    And save screenshot
    Then I should see "[TEST] LP 1"
    And I should see "[TEST] LP 2"
    And I should not see "[TEST] LP 3"
    And I should not see "[TEST] LP 4"
    When I am logged in as "test.approver"
    Then I go to "/admin/content/assigned-to-me"
    And save screenshot
    And I should see "[TEST] LP 4"
    And I should not see "[TEST] LP 1"
    And I should not see "[TEST] LP 2"
    And I should not see "[TEST] LP 3"
    When I am logged in as "test.admin"
    Then I go to "/admin/content/assigned-to-me"
    And save screenshot
    And I should see "[TEST] LP 3"
    And I should not see "[TEST] LP 1"
    And I should not see "[TEST] LP 2"
    And I should not see "[TEST] LP 4"



