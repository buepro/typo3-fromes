.. include:: ../Includes.txt

.. _tutorialAddSubfilter:

=============
Add subfilter
=============

**Audience:** Developers

As an example for this tutorial we assume a website offers sports activity
training management using the
`extension grevman <https://extensions.typo3.org/extension/grevman>`__. Now
the trainers from the various sport activities would like to have the
possibility to notify members regarding an upcoming event. In this tutorial we
walk through the steps involved to add a subfilter allowing to select members
from an upcoming event group.

.. rst-class:: bignums

#. Add typoscript to configure the subfilter

   The subfilter is mainly configured with typoscript. For our case we would add
   the following configuration:

   .. code-block:: typoscript
      :linenos:

      plugin.tx_fromes_messenger {
        settings {
          filter {
            includedSubfilters = feUserGroup, upcomingEvents
            class = \Vendor\Project\Domain\Model\Filter
          }
          subfilters {
            upcomingEvents {
              componentId = fm-upcoming-events
              class = \Vendor\Project\Domain\Model\EventSubfilter
              items = TypoScript
              items {
                table = tx_grevman_domain_model_event
                select {
                  pidInList = 6
                  where.dataWrap = startdate >= {date:U}
                  orderBy = startdate ASC
                  max = 10
                }
                fieldMap {
                  id = uid
                  label {
                    field = startdate
                    date = d.m.
                    noTrimWrap = | - ||
                    prepend = TEXT
                    prepend.field = title
                    prepend.crop = 30
                  }
                }
              }
            }
          }
        }
      }

   *  In line 5 we define the class that sets up the filter join clauses for the
      data base queries. Yes, its not a typo...several queries can be defined.
   *  In line 8 we define the subfilter. The subfilter name `upcomingEvents`
      is as well being added to the line 4.
   *  In line 9 we define the html id from the web component
   *  In line 10 we define the class that provides the configuration to the
      web component and modifies the data base query according its status.
   *  From line 11 onwards to line 28 we configure the items for the web
      component:

      *  From line 13 to 19 the available subfilter items are defined with
         a typoscript select function.
      *  From line 20 to 28 we map the data base result row to the subfilter
         item properties. A subfilter item has the properties `id` and
         `label`. The `id` won't be exposed directly in the web component.

      .. note::

         The interpretation from the items section (line 11-28) depends on
         the class defined for the subfilter.

#. Create `Filter` class

   .. code-block:: php
      :linenos:

      class Filter extends \Buepro\Fromes\Domain\Model\Filter
      {
          public function setupQueryBuilders(QueryBuilder $queryBuilder): FilterInterface
          {
              parent::setupQueryBuilders($queryBuilder);
              $this->setJoinForGroupRegistration();
              return $this;
          }

          public function getQueryBuilder(string $name): ?QueryBuilder
          {
              if ($name === 'default' || $name === 'group') {
                  return $this->queryBuilders['default'] ?? null;
              }
              return $this->queryBuilders[$name] ?? null;
          }

          private function setJoinForGroupRegistration(): void
          {
              if (($queryBuilder = $this->getQueryBuilder('group')) === null) {
                  return;
              }
              $queryBuilder->leftJoin(
                  'fe_users',
                  'tx_grevman_group_member_mm',
                  'group_member',
                  $queryBuilder->expr()->eq(
                      'group_member.uid_foreign',
                      $queryBuilder->quoteIdentifier('fe_users.uid')
                  )
              );
              $queryBuilder->leftJoin(
                  'group_member',
                  'tx_grevman_event_group_mm',
                  'event_group',
                  $queryBuilder->expr()->eq(
                      'event_group.uid_foreign',
                      $queryBuilder->quoteIdentifier('group_member.uid_local')
                  )
              );
              $queryBuilder->leftJoin(
                  'event_group',
                  'tx_grevman_domain_model_registration',
                  'group_registration',
                  (string)$queryBuilder->expr()->andX(
                      $queryBuilder->expr()->eq(
                          'group_registration.event',
                          $queryBuilder->quoteIdentifier('event_group.uid_local')
                      ),
                      $queryBuilder->expr()->eq(
                          'group_registration.member',
                          $queryBuilder->quoteIdentifier('fe_users.uid')
                      )
                  )
              );
          }
      }

#. Create `EventSubfilter` class

   .. code-block:: php
      :linenos:

      class EventSubfilter extends SubfilterBase
      {
          public function modifyQueryBuilders(): void
          {
              if (count($this->status) === 0) {
                  return;
              }
              $this->modifyQueryBuilderForGroupRegistrations();
          }

          private function modifyQueryBuilderForGroupRegistrations(): void
          {
              if (($queryBuilder = $this->filter->getQueryBuilder('group')) === null) {
                  return;
              }
              $constraints = [];
              foreach ($this->status as $uid) {
                  $constraints[] = $queryBuilder->expr()->eq(
                      'event_group.uid_local',
                      $queryBuilder->createNamedParameter($uid, \PDO::PARAM_INT)
                  );
              }
              $queryBuilder->andWhere($queryBuilder->expr()->orX(...$constraints));
          }
      }

#. Add event subfilter component to template

   .. code-block:: html
      :linenos:

      <fm-filter class="fm-filter"
                 data-config="{filterConfig -> f:format.json()}"
                 id="fm-filter"
                 result-id="fm-result"
      >
         ...
         <fm-select-list class="fmc-subfilter fmc-panel fmc-p-0" id="fm-upcoming-events">
            <f:render partial="Components/ListHeader" arguments="{title: 'Upcoming events'}" />
            <f:render partial="Components/ListControl" />
            <f:render partial="Components/ListContent" />
         </fm-select-list>
         ...
      </fm-filter>
